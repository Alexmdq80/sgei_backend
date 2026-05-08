<?php

namespace App\Services;

use App\Models\Persona;
use App\Models\Usuario;
use App\Models\DistritoUsuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Notifications\VerifyEmailNotification;

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
                    'password' => Hash::make('Sgei!' . date('Y') . '_Auto'),
                    'verification_token' => Str::random(60),
                    'verification_token_created_at' => now(),
                    'estado' => 'email_pendiente'
                ]);

                // Notify for verification
                $user->notify(new VerifyEmailNotification($user->verification_token));
            }

            // Link persona to user
            $persona->update(['usuario_id' => $user->id]);
            $user->update(['estado' => 'activo']);

            return $user->fresh();
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

            if ($roleName === 'jefe_distrital') {
                DistritoUsuario::where('usuario_id', $user->id)->delete();
            }
        });
    }
}
