<?php

namespace App\Services;

use App\Models\Usuario;
use App\Models\Persona;
use App\Models\Escuela;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Notifications\VerifyEmailNotification;
use App\Notifications\AdminEmailChangeNotification;
use Illuminate\Support\Str;
use App\DTOs\User\CreateUserDTO;
use App\DTOs\User\UpdateUserProfileDTO;


class UserService
{
    public function __construct(
        protected PersonaService $personaService,
        protected CupofService $cupofService
    ) {
    }

    /**
     * Get a paginated list of users with optional filters.
     */
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        $user = auth()->user();
        $isJefeProvincial = $user?->hasRole('jefe_provincial');
        $isJefeRegional = $user?->hasRole('jefe_regional');
        $isJefeDistrital = $user?->hasRole('jefe_distrital');

        $query = Usuario::query()->with([
            'persona',
            'documentoTipo',
            'persona.escuelasPersonas.escuela',
            'persona.escuelasPersonas.role',
            'roles',
            'provinciaUsuario.provincia',
            'regionUsuario.region',
            'distritoUsuario.distrito'
        ]);

        // Enforce Jurisdiction for non-superusers
        if ($user && !$user->hasRole('superuser')) {
            if ($isJefeProvincial && $user->provinciaUsuario) {
                $filters['provincia_id'] = $user->provinciaUsuario->provincia_id;
            } elseif ($isJefeRegional && $user->regionUsuario) {
                $filters['region_id'] = $user->regionUsuario->region_id;
            } elseif ($isJefeDistrital && $user->distritoUsuario) {
                $filters['departamento_id'] = $user->distritoUsuario->departamento_id;
            } elseif ($user->hasAnyRole(Usuario::ROLES_EQUIPO_CONDUCCION)) {
                $filters['escuela_ids'] = $user->persona?->escuelasPersonas()->whereNotNull('verified_at')->pluck('escuela_id')->toArray() ?? [];
            }
        }

        // Apply Hierarchical Roles restriction for all Chiefs
        if ($isJefeProvincial || $isJefeRegional || $isJefeDistrital) {
            $hierarchicalRoles = Usuario::ROLES_EQUIPO_CONDUCCION;
            $globalHierarchicalRoles = ['jefe_provincial', 'jefe_regional', 'jefe_distrital'];

            $query->where(function ($q) use ($hierarchicalRoles, $globalHierarchicalRoles) {
                // Roles en Escuelas (Directivos, etc)
                $q->whereHas('persona.escuelasPersonas.role', function ($sq) use ($hierarchicalRoles) {
                    $sq->whereIn('name', $hierarchicalRoles);
                })
                    // O Roles Globales de Jefatura
                    ->orWhereHas('roles', function ($sq) use ($globalHierarchicalRoles) {
                        $sq->whereIn('name', $globalHierarchicalRoles);
                    })
                    // También ver usuarios que están esperando vinculación (pueden ser sus futuros directivos)
                    ->orWhere('estado', 'vinculacion_pendiente');
            });
        }

        // Filtros Jurisdiccionales (Local Scopes)
        if (!empty($filters['provincia_id'])) {
            $query->inProvincia($filters['provincia_id']);
        }

        if (!empty($filters['region_id'])) {
            $query->inRegion($filters['region_id']);
        }

        if (!empty($filters['departamento_id'])) {
            $query->inDepartamento($filters['departamento_id']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('nombre', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('documento_numero', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('email', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Filtro por Escuela específica (ID o CUE)
        if (!empty($filters['escuela_id'])) {
            $query->whereHas('persona.escuelasPersonas', function ($q) use ($filters) {
                $q->where('escuela_id', $filters['escuela_id']);
            });
        }

        if (!empty($filters['escuela_ids'])) {
            $query->whereHas('persona.escuelasPersonas', function ($q) use ($filters) {
                $q->whereIn('escuela_id', $filters['escuela_ids']);
            });
        }

        if (!empty($filters['cue_anexo'])) {
            $query->whereHas('persona.escuelasPersonas.escuela', function ($q) use ($filters) {
                $q->where('cue_anexo', $filters['cue_anexo']);
            });
        }

        // Filtro por Estado de Vinculación
        if (!empty($filters['vinculation'])) {
            if ($filters['vinculation'] === 'vinculated') {
                $query->whereHas('persona.escuelasPersonas', function ($q) {
                    $q->whereNotNull('verified_at');
                });
            } elseif ($filters['vinculation'] === 'pending') {
                $query->whereHas('persona.escuelasPersonas', function ($q) {
                    $q->whereNull('verified_at');
                });
            }
        }

        // Filtro por Rol / Cargo
        if (!empty($filters['role'])) {
            $role = $filters['role'];
            $query->where(function ($q) use ($role) {
                if ($role === 'superuser') {
                    $q->where('es_administrador', true)
                        ->orWhereHas('roles', fn($sq) => $sq->where('name', 'superuser'));
                } elseif ($role === 'equipo_directivo') {
                    $leadershipRoles = Usuario::ROLES_EQUIPO_CONDUCCION;
                    $q->whereHas('persona.escuelasPersonas.role', fn($sq) => $sq->whereIn('name', $leadershipRoles))
                        ->orWhereHas('roles', fn($sq) => $sq->whereIn('name', $leadershipRoles));
                } else {
                    $q->whereHas('roles', fn($sq) => $sq->where('name', $role))
                        ->orWhereHas('persona.escuelasPersonas.role', fn($sq) => $sq->where('name', $role));
                }
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Create a new user (Administrative).
     */
    public function create(CreateUserDTO|array $data): Usuario
    {
        $dto = $data instanceof CreateUserDTO ? $data : CreateUserDTO::fromArray($data);

        $arrayData = $dto->toArray();
        $password = !empty($dto->password) ? $dto->password : 'Sgei!2026_Admin';
        $arrayData['password'] = Hash::make($password);

        $arrayData['verification_token'] = $dto->verificationToken ?? Str::random(60);
        $arrayData['verification_token_created_at'] = $dto->verificationTokenCreatedAt ?? now();

        $user = Usuario::create($arrayData);

        // Intentar vincular inmediatamente si existe coincidencia en el padrón (por DNI y Email)
        $this->linkToPersona($user);

        if (!$user->hasVerifiedEmail()) {
            // Enviar notificación de verificación si aún no lo está
            $user->notify(new VerifyEmailNotification($user->verification_token));
        }

        return $user;
    }

    /**
     * Link the user to a persona if identification matches and email is verified.
     * Requires match: documento_tipo_id, documento_numero AND Contacto->email.
     * If match is found, sets status to 'vinculacion_pendiente' for admin confirmation.
     */
    public function linkToPersona(Usuario $user): void
    {
        // If the user is already linked technically, we don't need to do anything here.
        // They are already identified.
        if ($user->persona) {
            return;
        }

        if (!$user->documento_tipo_id || !$user->documento_numero) {
            return;
        }

        // Search for a persona with matching documents and matching email in contact info
        $persona = Persona::where('documento_tipo_id', $user->documento_tipo_id)
            ->where('documento_numero', $user->documento_numero)
            ->whereHas('contacto', function ($query) use ($user) {
                $query->where('email', $user->email);
            })
            ->whereNull('usuario_id') // Only link if not already linked
            ->first();

        if ($persona) {
            // Match found: set to pending confirmation. 
            // The user will still need to verify email if they haven't yet, 
            // but they are now visible to their administrators.
            $user->update(['estado' => 'vinculacion_pendiente']);
        }
    }

    /**
     * Link a persona to an existing user if a match is found.
     * Useful when creating or updating a persona's contact information or DNI.
     * Match requirements: documento_tipo_id, documento_numero AND email match.
     * 
     * NEW RULES:
     * - Any match found results in 'vinculacion_pendiente' status for the user.
     * - Automatic linking is disabled to enforce admin confirmation.
     */
    public function linkPersonaToUser(Persona $persona): void
    {
        // If the persona is already linked to a user, do nothing.
        if ($persona->usuario_id) {
            return;
        }

        if (!$persona->documento_tipo_id || !$persona->documento_numero) {
            return;
        }

        // Load contact information to get the email
        $persona->loadMissing('contacto');
        if (!$persona->contacto || !$persona->contacto->email) {
            return;
        }

        $documentoNumeroRaw = $persona->getRawOriginal('documento_numero');

        // Search for a user with matching documents AND matching email
        $user = Usuario::where('documento_tipo_id', $persona->documento_tipo_id)
            ->where('documento_numero', $documentoNumeroRaw)
            ->where('email', $persona->contacto->email)
            ->first();

        // If user found and NOT already linked to ANY persona, set to pending confirmation.
        if ($user && !$user->persona) {
            // Match found: set to pending confirmation regardless of verification status
            $user->update(['estado' => 'vinculacion_pendiente']);
        }

        // NEW: Clear pending confirmation for users whose match was broken.
        // If a persona's email or DNI changed and no longer matches a user
        // that was waiting for confirmation, reset their state.
        Usuario::where('estado', 'vinculacion_pendiente')
            ->where('documento_tipo_id', $persona->documento_tipo_id)
            ->where('documento_numero', $documentoNumeroRaw)
            ->where('email', '!=', $persona->contacto->email)
            ->whereDoesntHave('persona') // Not linked to any persona
            ->get()
            ->each(function (Usuario $pendingUser) {
                $pendingUser->update([
                    'estado' => $pendingUser->hasVerifiedEmail() ? 'email_verificado' : 'email_pendiente',
                ]);
            });
    }

    /**
     * Resend the verification notification to the user.
     */
    public function resendVerification(Usuario $user): void
    {
        if ($user->hasVerifiedEmail()) {
            return;
        }

        $user->forceFill([
            'verification_token' => Str::random(60),
            'verification_token_created_at' => now(),
        ])->save();

        $user->notify(new VerifyEmailNotification($user->verification_token));
    }

    /**
     * Update the user's basic profile information.
     */
    public function updateProfile(Usuario $user, UpdateUserProfileDTO|array $data): \App\Models\Usuario
    {
        $dto = $data instanceof UpdateUserProfileDTO ? $data : UpdateUserProfileDTO::fromArray($data);
        $data = $dto->toArray();

        return \Illuminate\Support\Facades\DB::transaction(function () use ($user, $data, $dto) {
            // Handle password update if provided
            if (!empty($dto->password)) {
                $data['password'] = Hash::make($dto->password);
            } else {
                unset($data['password']); // Don't try to update password if empty
            }

            // Check for critical identity changes (Email or DNI)
            $emailChanged = isset($data['email']) && $data['email'] !== $user->email;
            $dniChanged = ($dto->documentoTipoId !== null && $dto->documentoTipoId != $user->documento_tipo_id)
                || ($dto->documentoNumero !== null && $dto->documentoNumero != $user->documento_numero);

            if ($emailChanged || $dniChanged) {
                $performer = \Illuminate\Support\Facades\Auth::user();
                $isAdmin = $performer?->es_administrador || $performer?->hasRole('superuser');

                // Special limit check for email changes (only for non-admins)
                if ($emailChanged && !$isAdmin && !$user->canChangeEmail()) {
                    throw ValidationException::withMessages([
                        'email' => ['Has alcanzado el límite máximo de cambios de correo electrónico (3).'],
                    ]);
                }

                // 1. Unlink from current persona if linked
                if ($user->persona) {
                    $this->personaService->unlinkUser($user->persona);
                }

                // 2. Data for matching
                $newDniTipo = $dto->documentoTipoId ?? $user->documento_tipo_id;
                $newDniNum = $dto->documentoNumero ?? $user->documento_numero;
                $newEmail = $data['email'] ?? $user->email;

                // 3. Check for coincidence with the NEW identity data
                $matchingPersona = Persona::where('documento_tipo_id', $newDniTipo)
                    ->where('documento_numero', $newDniNum)
                    ->whereHas('contacto', function ($query) use ($newEmail) {
                        $query->where('email', $newEmail);
                    })
                    ->whereNull('usuario_id')
                    ->first();

                // 4. Update core identity fields and status
                if ($emailChanged) {
                    $data['email_verified_at'] = null;
                    $data['verification_token'] = Str::random(60);
                    $data['verification_token_created_at'] = now();
                    $data['email_set_at'] = now();
                    $data['email_correction_attempts'] = $user->email_correction_attempts + 1;

                    // If matches a persona, it stays pending confirmation. Otherwise, pending verification.
                    $data['estado'] = $matchingPersona ? 'vinculacion_pendiente' : 'email_pendiente';
                } else if ($dniChanged) {
                    // DNI changed but email didn't. 
                    // If matches, pending admin confirmation. If not, clear pending state.
                    if ($matchingPersona) {
                        $data['estado'] = 'vinculacion_pendiente';
                    } else if (in_array($user->estado, ['activo', 'vinculacion_pendiente'])) {
                        // Was active or pending, now unlinked and no new match found.
                        $data['estado'] = $user->hasVerifiedEmail() ? 'email_verificado' : 'email_pendiente';
                    }
                }

                $user->update($data);

                // If email changed, notify for re-verification
                if ($emailChanged) {
                    $oldEmail = $user->getOriginal('email');
                    if ($isAdmin) {
                        $user->notify(new AdminEmailChangeNotification($user->verification_token, $oldEmail));
                    } else {
                        $user->notify(new VerifyEmailNotification($user->verification_token));
                    }
                }
            } else {
                $user->update($data);
            }

            // Refresh the model to reflect changes
            $user->refresh();

            // Try to link (only works if email is verified)
            $this->linkToPersona($user);

            return $user;
        });
    }

    /**
     * Assign or revoke a role from a user.
     */
    public function toggleRole(Usuario $user, string $roleName): void
    {
        if ($user->hasRole($roleName)) {
            $user->removeRole($roleName);
        } else {
            $user->assignRole($roleName);
        }
    }

    /**
     * Delete a user (Soft Delete).
     */
    public function delete(Usuario $user): bool
    {
        return (bool) $user->delete();
    }

    /**
     * Update the user's avatar and delete the old one if it exists.
     */
    public function updateAvatar(Usuario $user, UploadedFile $avatar): string
    {
        // Delete old avatar if exists
        if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        // Create a custom filename: user_id_timestamp.extension
        $timestamp = time();
        $extension = $avatar->getClientOriginalExtension();
        $filename = "{$user->id}_{$timestamp}.{$extension}";

        // Store new avatar in 'public/avatars' with the custom filename
        $path = $avatar->storeAs('avatars', $filename, 'public');

        $user->update(['avatar_path' => $path]);

        return asset('storage/' . $path);
    }

    /**
     * Delete the user's avatar.
     */
    public function deleteAvatar(Usuario $user): void
    {
        if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $user->update(['avatar_path' => null]);
    }

    /**
     * Get schools where the user has a verified "leadership team" role (equipo de conducción).
     * Leadership roles: director, vicedirector, secretario, prosecretario.
     */
    public function getAuthorizedSchoolsForProposals(Usuario $user): \Illuminate\Database\Eloquent\Collection
    {
        $leadershipRoles = Usuario::ROLES_EQUIPO_CONDUCCION;

        return Escuela::whereHas('escuelasPersonas', function ($query) use ($user, $leadershipRoles) {
            $query->whereHas('persona', function ($q) use ($user) {
                $q->where('usuario_id', $user->id);
            })
                ->whereNotNull('verified_at')
                ->whereHas('role', function ($q) use ($leadershipRoles) {
                    $q->whereIn('name', $leadershipRoles);
                });
        })->get();
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Usuario $user, string $currentPassword, string $newPassword): void
    {
        if (!Hash::check($currentPassword, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['La contraseña actual es incorrecta.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($newPassword)
        ]);
    }

    /**
     * Checks if a persona has any relationship with the schools where the user (performer) has roles.
     * Relationships: Active CUPOF, Enrollment (Inscripcion), or linked to an enrolled student.
     */
    public function isPersonaRelatedToUserSchools(Usuario $performer, Persona $persona): bool
    {
        // 1. Get performer's schools
        $performerSchoolIds = $performer->persona?->escuelasPersonas()->pluck('escuela_id')->toArray() ?? [];

        if (empty($performerSchoolIds)) {
            return false;
        }

        // 2. Check active CUPOF in those schools
        $hasCupof = $persona->movimientosCupofActivos()
            ->whereHas('cupof', function ($q) use ($performerSchoolIds) {
                $q->whereIn('escuela_id', $performerSchoolIds);
            })->exists();

        if ($hasCupof)
            return true;

        // 3. Check Enrollment (Inscripcion) in those schools
        $hasInscripcion = $persona->inscripcion()
            ->whereHas('espacio.propuesta', function ($q) use ($performerSchoolIds) {
                $q->whereIn('escuela_id', $performerSchoolIds);
            })->exists();

        if ($hasInscripcion)
            return true;

        // 4. Check Relationships with enrolled students (Vinculos)
        // Check if any student vinculated to this persona has an inscription in performer's schools
        $hasStudentVinculo = $persona->vinculosComoAdulto()
            ->whereHas('inscripcion.espacio.propuesta', function ($q) use ($performerSchoolIds) {
                $q->whereIn('escuela_id', $performerSchoolIds);
            })->exists();

        return $hasStudentVinculo;
    }

    /**
     * Busca personas candidatas a vincularse con un usuario.
     * Criterios: mismo documento_tipo_id, documento_numero y email (en contacto).
     * Filtra por jurisdicción del usuario logueado.
     */
    public function getCandidatosPersona(Usuario $usuario, Usuario $performer): \Illuminate\Database\Eloquent\Collection
    {

        if (!$usuario->documento_tipo_id || !$usuario->documento_numero || !$usuario->email) {
            return new \Illuminate\Database\Eloquent\Collection();
        }

        $query = Persona::where('documento_tipo_id', $usuario->documento_tipo_id)
            ->where('documento_numero', $usuario->documento_numero)
            ->whereHas('contacto', function ($q) use ($usuario) {
                $q->where('email', $usuario->email);
            })
            ->whereNull('usuario_id')
            ->with([
                'contacto',
                'documentoTipo',
                'movimientosCupofActivos.cupof.escuela.localidad.departamento',
                'inscripcion.escuelaProcedencia.localidad.departamento',
                'vinculosComoAdulto.inscripcion.escuelaProcedencia.localidad.departamento'
            ]);

        // Filtro jurisdiccional (solo para no-superusers)
        if (!$performer->hasRole('superuser') && !$performer->es_administrador) {
            if ($performer->hasRole('jefe_provincial')) {
                $provId = $performer->provinciaUsuario?->provincia_id;
                $query->inProvincia($provId);
            } elseif ($performer->hasRole('jefe_regional')) {
                $regId = $performer->regionUsuario?->region_id;
                $query->inRegion($regId);
            } elseif ($performer->hasRole('jefe_distrital')) {
                $distId = $performer->distritoUsuario?->departamento_id;
                $query->inDepartamento($distId);
            } elseif ($performer->hasAnyRole(Usuario::ROLES_EQUIPO_CONDUCCION)) {
                $escuelaIds = $performer->persona?->escuelasPersonas()->whereNotNull('verified_at')->pluck('escuela_id')->toArray() ?? [];
                if (empty($escuelaIds))
                    return collect();
                $query->where(function ($q) use ($escuelaIds) {
                    $q->whereHas('movimientosCupofActivos.cupof', function ($sq) use ($escuelaIds) {
                        $sq->whereIn('escuela_id', $escuelaIds);
                    })
                        ->orWhereHas('inscripcion.escuelaProcedencia', function ($sq) use ($escuelaIds) {
                            $sq->whereIn('escuela_id', $escuelaIds);
                        })
                        ->orWhereHas('vinculosComoAdulto.inscripcion.escuelaProcedencia', function ($sq) use ($escuelaIds) {
                            $sq->whereIn('escuela_id', $escuelaIds);
                        });
                });
            } else {
                return collect();
            }
        }


        return $query->limit(10)->get();
    }

}
