<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Persona;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Notifications\AccountInvitationNotification;
use App\DTOs\Persona\PersonaFilterDTO;
use App\DTOs\Persona\CreatePersonaDTO;
use App\DTOs\Persona\UpdatePersonaDTO;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use App\Notifications\UserUnlinkedNotification;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PersonaService
{
    /**
     * Create a new Persona in the database and handle initial contact details.
     */
    public function createPersona(CreatePersonaDTO $dto, ?string $cuilRaw = null, ?string $requestEmail = null): Persona
    {
         return DB::transaction(function () use ($dto, $cuilRaw, $requestEmail) {
            $personaData = $dto->toArray();

            // Indocumentado (tipo 7) sin numero: se autogenera el identificador provisorio IND-XXXXXX
            if ((int) ($personaData['documento_tipo_id'] ?? 0) === 7 && empty($personaData['documento_numero'] ?? '')) {
                $personaData['documento_numero'] = self::generarIdentificadorProvisorio();
            }
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
     * Genera el siguiente identificador provisorio para personas INDOCUMENTADAS (tipo 7).
     * Formato: IND-XXXXXX, correlativo a partir del mayor existente (o IND-000001).
     */
    private function generarIdentificadorProvisorio(): string
    {
        $ultimo = Persona::query()
            ->where('documento_tipo_id', 7)
            ->where('documento_numero', 'like', 'IND-%')
            ->whereRaw("documento_numero REGEXP '^IND-[0-9]+$'")
            ->orderByRaw('CAST(SUBSTRING(documento_numero, 5) AS UNSIGNED) DESC')
            ->value('documento_numero');

        $siguiente = 1;
        if ($ultimo !== null) {
            $siguiente = ((int) substr((string) $ultimo, 4)) + 1;
        }

        return 'IND-' . str_pad((string) $siguiente, 6, '0', STR_PAD_LEFT);
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
                if ($emailChanged || $dniChanged) {
                    throw ValidationException::withMessages([
                        'documento' => ['Operación Inválida: Esta persona tiene un usuario vinculado. No se permite modificar el DNI o el Email para preservar la integridad del vínculo.'],
                    ]);
                }
            }

            // Seguridad: no se permite marcar como fallecida una persona con usuario vinculado
            $pasandoAFallecida = $dto->viveSi === false && (bool) $persona->vive_si === true;
            if ($pasandoAFallecida && $persona->usuario_id) {
                throw ValidationException::withMessages([
                    'vive_si' => ['No se puede marcar como fallecida una persona que tiene un usuario vinculado. Primero desvincule al usuario.'],
                ]);
            }
            
            // Update persona model with non-null attributes from DTO
            $personaData = $dto->toPersonaArray();

            // Indocumentado (tipo 7) sin numero: conserva/autogenera el identificador provisorio
            if (((int) ($personaData['documento_tipo_id'] ?? 0)) === 7 && empty($personaData['documento_numero'] ?? '')) {
                $actual = $persona->documentoNumeroRaw();
                $personaData['documento_numero'] = (is_string($actual) && preg_match('/^IND-\d{6}$/', $actual))
                    ? $actual
                    : self::generarIdentificadorProvisorio();
            }

            $persona->update($personaData);

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
            'contacto',
            'usuario.roles',
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

        // --- Ordenamiento dinámico (whitelist p/ inyección SQL) ---
        $allowedSortColumns = [
            'apellido',
            'nombre',
            'documento_numero',
            'created_at',
            'updated_at',
        ];

        $sortBy = $filters->sortBy !== null && in_array($filters->sortBy, $allowedSortColumns)
            ? $filters->sortBy
            : null;

        $order = ($filters->order ?? 'asc') === 'desc' ? 'desc' : 'asc';

        if ($sortBy === 'apellido') {
            $query->orderBy('apellido', $order)->orderBy('nombre', $order);
        } elseif ($sortBy !== null) {
            $query->orderBy($sortBy, $order);
        } else {
            $query->orderBy('apellido')->orderBy('nombre');
        }

        return $query->paginate($filters->perPage ?? 10);
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
                    'password_set' => false,
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
                $linkedUser->notify(new UserUnlinkedNotification($persona->nombre, $persona->apellido));

                // 2. Eliminar vinculaciones institucionales (escuelas)
                \App\Models\EscuelaPersona::where('persona_id', $persona->id)->delete();

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
     * Revoca un rol administrativo global del usuario vinculado a una persona.
     * El rol 'superuser' nunca puede ser revocado a través de esta operación.
     */
    public function removeAdministrativeRole(Persona $persona, string $role): void
    {
        $user = $persona->usuario;

        if (!$user) {
            throw new \Exception('La persona no tiene un usuario vinculado.', 404);
        }

        if ($role === 'superuser') {
            throw new \Exception('No se puede revocar el rol de Superusuario a través de esta operación.', 403);
        }

        $user->removeRole($role);
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

            if ($user->estado === 'esperando_activacion') {
                $user->notify(new AccountInvitationNotification($user->verification_token));
            } else {
                $user->notify(new VerifyEmailNotification($user->verification_token));
            }
        });

        return $user->fresh();
    }
    /**
     * Store (or replace) a Persona's profile photo in private storage.
     */
    public function updateFoto(Persona $persona, UploadedFile $foto): string
    {
        // Delete the previous photo if it exists
        if ($persona->foto_path && Storage::disk('local')->exists($persona->foto_path)) {
            Storage::disk('local')->delete($persona->foto_path);
        }

        $timestamp = time();
        $extension = $foto->getClientOriginalExtension();
        $filename = "persona_{$persona->id}_{$timestamp}.{$extension}";

        $path = $foto->storeAs('personas', $filename, 'local');

        $persona->update(['foto_path' => $path]);

        return $persona->foto_url;
    }

    /**
     * Delete a Persona's profile photo.
     */
    public function deleteFoto(Persona $persona): void
    {
        if ($persona->foto_path && Storage::disk('local')->exists($persona->foto_path)) {
            Storage::disk('local')->delete($persona->foto_path);
        }

        $persona->update(['foto_path' => null]);
    }

}
