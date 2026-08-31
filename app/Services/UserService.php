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
use App\Notifications\AccountInvitationNotification;
use App\Events\UsuarioUpdatedEvent;
use Illuminate\Database\QueryException;

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

        $query = Usuario::query()->with([
            'persona',
            'documentoTipo',
            'persona.escuelasPersonas.escuela',
            'persona.escuelasPersonas.role',
            'roles'
        ]);

        // Enforce Jurisdiction for non-superusers
        if ($user && !$user->hasRole('superuser')) {
            if ($user->hasAnyRole(Usuario::ROLES_EQUIPO_CONDUCCION)) {
                $filters['escuela_ids'] = $user->persona?->escuelasPersonas()->whereNotNull('verified_at')->pluck('escuela_id')->toArray() ?? [];
            }
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

        // Filtro por Estado de Contraseña (password_set)
        if (isset($filters['password_set']) && $filters['password_set'] !== '') {
            $isSet = filter_var($filters['password_set'], FILTER_VALIDATE_BOOLEAN);
            $query->where('password_set', $isSet);
        }

        // Filtro por Email Verificado
        if (!empty($filters['email_verified'])) {
            if ($filters['email_verified'] === 'verified') {
                $query->whereNotNull('email_verified_at');
            } elseif ($filters['email_verified'] === 'unverified') {
                $query->whereNull('email_verified_at');
            }
        }

        // Filtro por Vinculación a Persona en Padrón
        if (!empty($filters['persona_linked'])) {
            if ($filters['persona_linked'] === 'linked') {
                $query->has('persona');
            } elseif ($filters['persona_linked'] === 'unlinked') {
                $query->doesntHave('persona');
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

        // --- Ordenamiento dinámico (con lista blanca para prevenir inyección SQL) ---
        $allowedSortColumns = [
            'nombre',
            'email',
            'documento_numero',
            'documento_tipo_id',
            'es_administrador',
            'estado',
            'email_verified_at',
            'password_set',
            'created_at',
            'updated_at',
        ];

        $sortBy = in_array($filters['sort_by'] ?? null, $allowedSortColumns)
            ? $filters['sort_by']
            : 'created_at';

        $order = ($filters['order'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        return $query->orderBy($sortBy, $order)->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Create a new user (Administrative).
     */
    public function create(CreateUserDTO|array $data): Usuario
    {
        $dto = $data instanceof CreateUserDTO ? $data : CreateUserDTO::fromArray($data);
        $arrayData = $dto->toArray();

        // 1. Si viene password en el DTO, se usa. Si no, se genera uno aleatorio temporal.
        $hasPassword = !empty($dto->password);
        $rawPassword = $hasPassword ? $dto->password : Str::random(32);

        $arrayData['password'] = Hash::make($rawPassword);
        $arrayData['password_set'] = $hasPassword; // Solo es true si vino explícita

        $arrayData['verification_token'] = $dto->verificationToken ?? Str::random(60);
        $arrayData['verification_token_created_at'] = $dto->verificationTokenCreatedAt ?? now();

        $user = Usuario::create($arrayData);

        // 2. Intentar vincular con el padrón
        $this->linkToPersona($user);

        // 3. Notificaciones según el caso:
        if (!$hasPassword) {
            // Enviar invitación para configurar contraseña
            $user->notify(new AccountInvitationNotification($user->verification_token));
        } elseif (!$user->hasVerifiedEmail()) {
            // Si ya tiene clave pero falta verificar email
            $user->notify(new VerifyEmailNotification($user->verification_token));
        }

        UsuarioUpdatedEvent::dispatch('created', $user->id);

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

        // Search for a persona with matching documents and matching email in contact info and vive_si === true 
        $persona = Persona::where('vive_si', 1)
            ->where('documento_tipo_id', $user->documento_tipo_id)
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
            UsuarioUpdatedEvent::dispatch('linked', $user->id);
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
        // Una persona fallecida nunca se auto-vincula a un usuario
        if (!$persona->vive_si) {
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
            UsuarioUpdatedEvent::dispatch('linked', $user->id);
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
    public function updateProfile(Usuario $user, UpdateUserProfileDTO|array $data): Usuario
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

                // Verificación proactiva: ¿otro usuario ya tiene este DNI?
                if (isset($data['documento_tipo_id']) || isset($data['documento_numero'])) {
                    $newDocTipo = $data['documento_tipo_id'] ?? $user->documento_tipo_id;
                    $newDocNumero = $data['documento_numero'] ?? $user->documento_numero;

                    $duplicate = Usuario::where('documento_tipo_id', $newDocTipo)
                        ->where('documento_numero', $newDocNumero)
                        ->where('id', '!=', $user->id)
                        ->exists();

                    if ($duplicate) {
                        throw ValidationException::withMessages([
                            'documento_numero' => [
                                'Ya existe otro usuario con el mismo tipo y número de documento. No se pueden duplicar documentos entre usuarios.',
                            ],
                        ]);
                    }
                }

                try {
                    $user->update($data);
                } catch (QueryException $e) {
                    if ($e->getCode() === '23000' || str_contains($e->getMessage(), 'Duplicate entry')) {
                        throw ValidationException::withMessages([
                            'documento_numero' => [
                                'Ya existe otro usuario con el mismo tipo y número de documento. No se pueden duplicar documentos entre usuarios.',
                            ],
                        ]);
                    }
                    throw $e;
                }

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

            UsuarioUpdatedEvent::dispatch('updated', $user->id);

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
        UsuarioUpdatedEvent::dispatch('updated', $user->id);

    }

    /**
     * Delete a user (Soft Delete).
     */
    public function delete(Usuario $user): bool
    {
        $result = (bool) $user->delete();
        UsuarioUpdatedEvent::dispatch('deleted', $user->id);
        return $result;
    }

    /**
     * Update the user's avatar and delete the old one if it exists.
     */
    public function updateAvatar(Usuario $user, UploadedFile $avatar): string
    {
        if ($user->avatar_path && Storage::disk('local')->exists($user->avatar_path)) {
            Storage::disk('local')->delete($user->avatar_path);
        }

        $timestamp = time();
        $extension = $avatar->getClientOriginalExtension() ?: 'jpg';
        $filename = "{$user->id}_{$timestamp}.{$extension}";

        $path = $avatar->storeAs('avatars', $filename, 'local');

        if (!$path) {
            throw new \RuntimeException('No se pudo almacenar el archivo de avatar.');
        }

        $user->update(['avatar_path' => $path]);
        $user->refresh();

        return $user->avatar_url ?? url("/api/v1/usuarios/{$user->id}/avatar");
    }


    /**
     * Delete the user's avatar.
     */
    public function deleteAvatar(Usuario $user): void
    {
        if ($user->avatar_path && Storage::disk('local')->exists($user->avatar_path)) {
            Storage::disk('local')->delete($user->avatar_path);
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

        $query = Persona::where('vive_si', 1)
            ->where('documento_tipo_id', $usuario->documento_tipo_id)
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
            if ($performer->hasAnyRole(Usuario::ROLES_EQUIPO_CONDUCCION)) {
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

    /**
     * Resend the account activation invitation (pure invitation to set a password).
     * Does NOT touch email_verified_at, estado, or the password itself.
     */
    public function resendActivation(Usuario $user): void
    {
        if ($user->password_set) {
            throw ValidationException::withMessages([
                'password' => ['Este usuario ya tiene una contraseña establecida.'],
            ]);
        }

        $user->forceFill([
            'verification_token' => Str::random(60),
            'verification_token_created_at' => now(),
        ])->save();

        $user->notify(new AccountInvitationNotification($user->verification_token));
        UsuarioUpdatedEvent::dispatch('updated', $user->id);
    }
    /**
     * Resend the email verification notification.
     * Does NOT touch the password or password_set.
     */
    public function resendEmailVerification(Usuario $user): void
    {
        if ($user->hasVerifiedEmail()) {
            throw ValidationException::withMessages([
                'email' => ['El email ya está verificado.'],
            ]);
        }

        $user->forceFill([
            'verification_token' => Str::random(60),
            'verification_token_created_at' => now(),
        ])->save();

        $user->notify(new VerifyEmailNotification($user->verification_token));
        UsuarioUpdatedEvent::dispatch('updated', $user->id);

    }

}
