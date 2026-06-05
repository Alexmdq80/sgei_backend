<?php

namespace App\Services;

use App\Models\Usuario;
use App\Models\Persona;
use App\Models\Escuela;
use App\Models\EscuelaUsuario;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Support\Str;

class UserService
{
    /**
     * Get a paginated list of users with optional filters.
     */
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        $user = auth()->user();
        $isJefeDistrital = $user?->hasRole('jefe_distrital');

        $query = Usuario::query()->with([
            'persona', 
            'documentoTipo', 
            'escuelaUsuarios.escuela', 
            'escuelaUsuarios.role',
            'roles'
        ]);

        if ($isJefeDistrital) {
            $hierarchicalRoles = \App\Services\EscuelaService::HIERARCHICAL_ROLES;
            $query->whereHas('escuelaUsuarios.role', function ($q) use ($hierarchicalRoles) {
                $q->whereIn('name', $hierarchicalRoles);
            });
        }

        // Filtros Jurisdiccionales
        if (!empty($filters['provincia_id'])) {
            $query->where(function ($q) use ($filters) {
                $q->whereHas('provinciaUsuario', function ($pq) use ($filters) {
                    $pq->where('provincia_id', $filters['provincia_id']);
                })->orWhereHas('escuelaUsuarios.escuela.localidad.departamento', function ($ld) use ($filters) {
                    $ld->where('provincia_id', $filters['provincia_id']);
                });
            });
        }

        if (!empty($filters['region_id'])) {
            $query->where(function ($q) use ($filters) {
                $q->whereHas('regionUsuario', function ($rq) use ($filters) {
                    $rq->where('region_id', $filters['region_id']);
                })->orWhereHas('escuelaUsuarios.escuela.localidad.departamento', function ($ld) use ($filters) {
                    $ld->where('region_id', $filters['region_id']);
                });
            });
        }

        if (!empty($filters['departamento_id'])) {
            $query->where(function ($q) use ($filters) {
                $q->whereHas('distritoUsuario', function ($dq) use ($filters) {
                    $dq->where('departamento_id', $filters['departamento_id']);
                })->orWhereHas('escuelaUsuarios.escuela.localidad', function ($l) use ($filters) {
                    $l->where('departamento_id', $filters['departamento_id']);
                });
            });
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
            $query->whereHas('escuelaUsuarios', function ($q) use ($filters) {
                $q->where('escuela_id', $filters['escuela_id']);
            });
        }

        if (!empty($filters['escuela_ids'])) {
            $query->whereHas('escuelaUsuarios', function ($q) use ($filters) {
                $q->whereIn('escuela_id', $filters['escuela_ids']);
            });
        }

        if (!empty($filters['cue_anexo'])) {
            $query->whereHas('escuelaUsuarios.escuela', function ($q) use ($filters) {
                $q->where('cue_anexo', $filters['cue_anexo']);
            });
        }

        // Filtro por Estado de Vinculación
        if (!empty($filters['vinculation'])) {
            if ($filters['vinculation'] === 'vinculated') {
                $query->whereHas('escuelaUsuarios', function ($q) {
                    $q->whereNotNull('verified_at');
                });
            } elseif ($filters['vinculation'] === 'pending') {
                $query->whereHas('escuelaUsuarios', function ($q) {
                    $q->whereNull('verified_at');
                });
            }
        }

        return $query->orderBy('created_at', 'desc')->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Create a new user (Administrative).
     */
    public function create(array $data): Usuario
    {
        // Asegurar contraseña: si está vacía, usar el default del sistema
        $password = !empty($data['password']) ? $data['password'] : 'Sgei!2026_Admin';
        $data['password'] = Hash::make($password);
        
        $data['verification_token'] = Str::random(60);
        $data['verification_token_created_at'] = now();

        $user = Usuario::create($data);

        // Si el admin lo crea ya verificado (o si por alguna razón ya tiene email_verified_at)
        if ($user->hasVerifiedEmail()) {
            $this->linkToPersona($user);
        } else {
            // Enviar notificación de verificación
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
        if (!$user->hasVerifiedEmail()) {
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
            // Match found + Email IS Verified (checked at start of method)
            // The user says for THIS case (verified match during registration/verification)
            // wait, should this be pending too?
            // "si al momento de registrar el usuario existe coincidencia... dejar pendiente"
            // "si al momento de crear una persona, existe coincidencia... y verificado... automática"
            
            // I'll keep linkToPersona (user registration side) as PENDING as requested before,
            // unless the user meant "all verified matches are automatic".
            // BUT the user specifically said "si al momento de crear una persona... automática".
            // Let's assume the difference is who initiates the action.
            // If ADMIN creates Persona -> Match Verified User -> Link Automatic.
            // If USER creates Account -> Match Persona -> Link Pending.
            
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

        // Search for a user with matching documents AND matching email
        $user = Usuario::where('documento_tipo_id', $persona->documento_tipo_id)
                       ->where('documento_numero', $persona->documento_numero)
                       ->where('email', $persona->contacto->email)
                       ->first();

        if ($user && !$user->persona) {
            // Match found: set to pending confirmation regardless of verification status
            $user->update(['estado' => 'vinculacion_pendiente']);
        }
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
    public function updateProfile(Usuario $user, array $data): \App\Models\Usuario
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($user, $data) {
            // Handle password update if provided
            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']); // Don't try to update password if empty
            }

            // Check for critical identity changes (Email or DNI)
            $emailChanged = isset($data['email']) && $data['email'] !== $user->email;
            $dniChanged = (isset($data['documento_tipo_id']) && $data['documento_tipo_id'] != $user->documento_tipo_id) ||
                         (isset($data['documento_numero']) && $data['documento_numero'] != $user->documento_numero);

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
                    $user->persona->update(['usuario_id' => null]);
                }

                // 2. Data for matching
                $newDniTipo = $data['documento_tipo_id'] ?? $user->documento_tipo_id;
                $newDniNum = $data['documento_numero'] ?? $user->documento_numero;
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
                    // If matches, pending admin confirmation. If not, stays as is (active or verified).
                    if ($matchingPersona) {
                        $data['estado'] = 'vinculacion_pendiente';
                    } else if ($user->estado === 'activo') {
                        // Was active (linked), now unlinked and no new match found.
                        $data['estado'] = 'email_verificado'; 
                    }
                }
                
                $user->update($data);
                
                // If email changed, notify for re-verification
                if ($emailChanged) {
                    $user->notify(new VerifyEmailNotification($user->verification_token));
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
        $leadershipRoles = ['director', 'vicedirector', 'secretario', 'prosecretario'];

        return Escuela::whereHas('escuelaUsuarios', function ($query) use ($user, $leadershipRoles) {
            $query->where('usuario_id', $user->id)
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
        $performerSchoolIds = $performer->escuelaUsuarios()->pluck('escuela_id')->toArray();
        
        if (empty($performerSchoolIds)) {
            return false;
        }

        // 2. Check active CUPOF in those schools
        $hasCupof = $persona->movimientosCupofActivos()
            ->whereHas('cupof', function ($q) use ($performerSchoolIds) {
                $q->whereIn('escuela_id', $performerSchoolIds);
            })->exists();

        if ($hasCupof) return true;

        // 3. Check Enrollment (Inscripcion) in those schools
        $hasInscripcion = $persona->inscripcion()
            ->whereHas('espacio.propuesta', function ($q) use ($performerSchoolIds) {
                $q->whereIn('escuela_id', $performerSchoolIds);
            })->exists();

        if ($hasInscripcion) return true;

        // 4. Check Relationships with enrolled students (Vinculos)
        // Check if any student vinculated to this persona has an inscription in performer's schools
        $hasStudentVinculo = $persona->vinculosComoAdulto()
            ->whereHas('inscripcion.espacio.propuesta', function ($q) use ($performerSchoolIds) {
                $q->whereIn('escuela_id', $performerSchoolIds);
            })->exists();

        return $hasStudentVinculo;
    }
}
