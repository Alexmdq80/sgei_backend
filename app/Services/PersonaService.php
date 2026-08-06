<?php

namespace App\Services;

use App\Models\Persona;
use App\Models\Usuario;
use App\Models\DistritoUsuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Notifications\AccountInvitationNotification;
use App\DTOs\Persona\PersonaFilterDTO;
use App\DTOs\Persona\CreatePersonaDTO;
use App\DTOs\Persona\UpdatePersonaDTO;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Exceptions\ConfirmationRequiredException;

class PersonaService
{
    /**
     * Create a new Persona in the database and handle initial contact details.
     */
    public function createPersona(CreatePersonaDTO $dto, ?string $cuilRaw = null, ?string $requestEmail = null): Persona
    {
        return DB::transaction(function () use ($dto, $cuilRaw, $requestEmail) {
            $personaData = $dto->toArray();

            // Handle raw CUIL string if formatted as XX-XXXXXXXX-X
            if (!empty($cuilRaw)) {
                $parts = explode('-', str_replace([' ', '.'], '', $cuilRaw));
                if (count($parts) === 3) {
                    $personaData['CUIL_prefijo'] = $parts[0];
                    $personaData['CUIL_sufijo'] = $parts[2];
                }
            }

            $persona = Persona::create($personaData);

            $emailToSave = $dto->email ?? $requestEmail;
            if (!empty($emailToSave)) {
                $persona->contacto()->create([
                    'email' => $emailToSave,
                ]);
            }

            return $persona->fresh(['contacto', 'usuario']);
        });
    }

    /**
     * Update an existing Persona record and manage security rules for identity changes.
     */
    public function updatePersona(Persona $persona, UpdatePersonaDTO $dto, bool $hasEmailInPayload = false): Persona
    {
        return DB::transaction(function () use ($persona, $dto, $hasEmailInPayload) {
            // Security check: control identity changes (DNI or Email)
            $emailChanged = $hasEmailInPayload && $dto->email !== ($persona->contacto?->email ?? null);

            $dniChanged = $dto->documentoIdentidad !== null
                && ($dto->documentoIdentidad->tipoId() !== (int) $persona->documento_tipo_id
                    || $dto->documentoIdentidad->numero() !== $persona->documentoNumeroRaw());

            if ($persona->usuario_id) {
                if ($emailChanged) {
                    // Confirmación explícita requerida (HTTP 409)
                    if (!$dto->confirmed) {
                        throw new ConfirmationRequiredException(
                            action: 'CONFIRM_UNLINK_USER',
                            message: 'Cambiar el email desvinculará al usuario vinculado y revocará todos sus roles. ¿Confirmás la acción?',
                            context: [
                                'usuario_id'    => $persona->usuario_id,
                                'usuario_email' => $persona->usuario->email ?? null,
                                'nuevo_email'   => $dto->email,
                            ]
                        );
                    }
                    $this->unlinkUser($persona);
                } elseif ($dniChanged) {
                    $this->unlinkUser($persona);
                }
            }

            // Update persona model with non-null attributes from DTO
            $persona->update($dto->toPersonaArray());

            // Update or clear contact email
            if ($hasEmailInPayload) {
                $newEmail = !empty($dto->email) ? $dto->email : null;
                $persona->contacto()->updateOrCreate(
                    ['persona_id' => $persona->id],
                    ['email' => $newEmail]
                );
            }

            // If DNI changed, attempt to link matching user
            if ($dniChanged || $emailChanged) {
                $persona->refresh();
                app(UserService::class)->linkPersonaToUser($persona);
            }

            return $persona->fresh(['contacto', 'usuario']);
        });
    }

    /**
     * Safely delete a Persona record.
     */
    public function deletePersona(Persona $persona): bool
    {
        return DB::transaction(function () use ($persona) {
            return (bool) $persona->delete();
        });
    }
    /**
     * Get paginated list of Personas filtered by PersonaFilterDTO criteria.
     */
    public function getFilteredPaginated(PersonaFilterDTO $filters): LengthAwarePaginator
    {
        $query = Persona::with([
            'documentoTipo', 
            'usuario.roles', 
            'usuario.provinciaUsuario.provincia', 
            'usuario.regionUsuario.region', 
            'usuario.distritoUsuario.distrito',
            'nacionalidad', 
            'genero'
        ]);

        // 1. Search term (nombre, apellido, documento)
        if ($filters->search) {
            $search = $filters->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('apellido', 'like', "%{$search}%")
                  ->orWhere('documento_numero', 'like', "%{$search}%");
            });
        }

        // 2. Only agents (people with CUPOF movements)
        if ($filters->onlyAgents) {
            $query->whereHas('movimientosCupof', function ($q) use ($filters) {
                if ($filters->escuelaId) {
                    $q->whereHas('cupof', function ($sq) use ($filters) {
                        $sq->where('escuela_id', $filters->escuelaId);
                    });
                }
            });
        }

        // 3. Geographic & Demographic filters
        if ($filters->provinciaId) {
            $query->where('provincia_id', $filters->provinciaId);
        }

        if ($filters->departamentoId) {
            $query->where('departamento_id', $filters->departamentoId);
        }

        if ($filters->localidadId) {
            $query->where('localidad_id', $filters->localidadId);
        }

        if ($filters->nacionalidadNacionId) {
            $query->where('nacionalidad_nacion_id', $filters->nacionalidadNacionId);
        }

        if ($filters->sexoId) {
            $query->where('sexo_id', $filters->sexoId);
        }

        if ($filters->generoId) {
            $query->where('genero_id', $filters->generoId);
        }

        if ($filters->documentoTipoId) {
            $query->where('documento_tipo_id', $filters->documentoTipoId);
        }

        // 4. Linked user filter
        if ($filters->hasUser !== null) {
            if ($filters->hasUser) {
                $query->whereNotNull('usuario_id');
            } else {
                $query->whereNull('usuario_id');
            }
        }

        return $query->orderBy('apellido')->orderBy('nombre')->paginate($filters->perPage ?? 15);
    }
    /**
     * Ensures that a Persona has an associated Usuario account.
     * If not, it creates one based on Persona's data.
     */
    public function ensureUserExists(Persona $persona): Usuario
    {
        if ($persona->usuario_id && $persona->usuario) {
            return $persona->usuario;
        }

        return DB::transaction(function () use ($persona) {
            $persona->loadMissing('contacto');
            
            if (!$persona->contacto || !$persona->contacto->email) {
                throw new \Exception("La persona debe tener un email de contacto registrado para crear una cuenta de usuario.");
            }

            // Check if a user with this email already exists but is not linked
            $user = Usuario::where('email', $persona->contacto->email)->first();

            if (!$user) {
                $user = Usuario::create([
                    'nombre' => $persona->nombre . ' ' . $persona->apellido,
                    'documento_tipo_id' => $persona->documento_tipo_id,
                    'documento_numero' => $persona->documento_numero,
                    'email' => $persona->contacto->email,
                    // Usamos una contraseña aleatoria segura, el usuario la cambiará en la activación
                    'password' => Hash::make(Str::random(32)),
                    'verification_token' => Str::random(60),
                    'verification_token_created_at' => now(),
                    'estado' => 'esperando_activacion'
                ]);

                // Notify for activation/invitation
                $user->notify(new AccountInvitationNotification($user->verification_token));
            }

            // Link persona to user
            $persona->update(['usuario_id' => $user->id]);
            
            // Sincronizar roles basados en CUPOF
            app(\App\Services\CupofService::class)->syncAllRolesFromCupof($user);
            
            // Importante: No ponemos 'activo' todavía, el estado final lo pondrá el flujo de activación
            
            return $user->fresh();
        });
    }

    /**
     * Assigns the Jefe Provincial role and its geographical context.
     */
    public function assignJefeProvincial(Persona $persona, int $provinciaId): Usuario
    {
        return DB::transaction(function () use ($persona, $provinciaId) {
            $user = $this->ensureUserExists($persona);

            if (!$user->hasRole('jefe_provincial')) {
                $user->assignRole('jefe_provincial');
            }

            \App\Models\ProvinciaUsuario::updateOrCreate(
                ['usuario_id' => $user->id],
                ['provincia_id' => $provinciaId]
            );

            return $user;
        });
    }

    /**
     * Assigns the Jefe Regional role and its geographical context.
     */
    public function assignJefeRegional(Persona $persona, int $regionId): Usuario
    {
        return DB::transaction(function () use ($persona, $regionId) {
            $user = $this->ensureUserExists($persona);

            if (!$user->hasRole('jefe_regional')) {
                $user->assignRole('jefe_regional');
            }

            \App\Models\RegionUsuario::updateOrCreate(
                ['usuario_id' => $user->id],
                ['region_id' => $regionId]
            );

            return $user;
        });
    }

    /**
     * Assigns the Jefe Distrital role and its geographical context.
     */
    public function assignJefeDistrital(Persona $persona, string $departamentoId): Usuario
    {
        return DB::transaction(function () use ($persona, $departamentoId) {
            $user = $this->ensureUserExists($persona);

            if (!$user->hasRole('jefe_distrital')) {
                $user->assignRole('jefe_distrital');
            }

            DistritoUsuario::updateOrCreate(
                ['usuario_id' => $user->id],
                ['departamento_id' => $departamentoId]
            );

            return $user;
        });
    }

    /**
     * Assigns the Supervisor Curricular role.
     */
    public function assignSupervisor(Persona $persona): Usuario
    {
        return DB::transaction(function () use ($persona) {
            $user = $this->ensureUserExists($persona);

            if (!$user->hasRole('supervisor_curricular')) {
                $user->assignRole('supervisor_curricular');
            }

            return $user;
        });
    }

    /**
     * Removes an administrative role and its context if applicable.
     */
    public function removeAdministrativeRole(Persona $persona, string $roleName): void
    {
        $user = $persona->usuario;
        if (!$user) return;

        DB::transaction(function () use ($user, $roleName) {
            if ($user->hasRole($roleName)) {
                $user->removeRole($roleName);
            }

            switch ($roleName) {
                case 'jefe_provincial':
                    \App\Models\ProvinciaUsuario::where('usuario_id', $user->id)->delete();
                    break;
                case 'jefe_regional':
                    \App\Models\RegionUsuario::where('usuario_id', $user->id)->delete();
                    break;
                case 'jefe_distrital':
                    DistritoUsuario::where('usuario_id', $user->id)->delete();
                    break;
            }
        });
    }

    /**
     * Desvincula un usuario de una persona, revoca todos sus roles (excepto superuser)
     * y elimina todas sus vinculaciones institucionales y geográficas.
     */
    public function unlinkUser(Persona $persona): void
    {
        $linkedUser = $persona->usuario;

        DB::transaction(function () use ($persona, $linkedUser) {
            // 1. Desvincular técnicamente a la persona del usuario
            $persona->update(['usuario_id' => null]);

            if ($linkedUser) {
                // 2. Eliminar vinculaciones institucionales (escuelas)
                \App\Models\EscuelaPersona::where('persona_id', $persona->id)->delete();

                // 3. Eliminar vinculaciones geográficas de roles de jefatura
                \App\Models\ProvinciaUsuario::where('usuario_id', $linkedUser->id)->delete();
                \App\Models\RegionUsuario::where('usuario_id', $linkedUser->id)->delete();
                DistritoUsuario::where('usuario_id', $linkedUser->id)->delete();

                // 4. Revocar todos los roles de Spatie, preservando 'superuser' si lo tuviera
                $rolesToKeep = [];
                if ($linkedUser->hasRole('superuser')) {
                    $rolesToKeep[] = 'superuser';
                }
                $linkedUser->syncRoles($rolesToKeep);

                // 5. El usuario vuelve a un estado según su verificación de email
                $newState = $linkedUser->hasVerifiedEmail() ? 'email_verificado' : 'email_pendiente';
                $linkedUser->update(['estado' => $newState]);
            }
        });
    }

    /**
     * Manually resends the activation email for a Persona.
     */
    public function resendActivation(Persona $persona): Usuario
    {
        $user = $persona->usuario;
        if (!$user) {
            throw new \Exception("La persona no tiene una cuenta de usuario vinculada.");
        }

        if ($user->hasVerifiedEmail() || $user->estado === 'activo') {
            throw new \Exception("La cuenta de esta persona ya se encuentra activa.");
        }

        DB::transaction(function () use ($user) {
            $user->update([
                'verification_token' => Str::random(60),
                'verification_token_created_at' => now(),
                //'estado' => 'esperando_activacion'
            ]);

            $user->notify(new AccountInvitationNotification($user->verification_token));
        });

        return $user->fresh();
    }
}
