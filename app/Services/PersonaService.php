<?php

namespace App\Services;

use App\Models\Persona;
use App\Models\Usuario;
use App\Models\DistritoUsuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Notifications\AccountInvitationNotification;

class PersonaService
{
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
                'estado' => 'esperando_activacion'
            ]);

            $user->notify(new AccountInvitationNotification($user->verification_token));
        });

        return $user->fresh();
    }
}
