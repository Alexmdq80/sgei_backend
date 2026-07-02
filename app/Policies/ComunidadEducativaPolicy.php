<?php

namespace App\Policies;

use App\Models\Escuela;
use App\Models\Usuario;

class ComunidadEducativaPolicy
{
    /**
     * Determine whether the user can view the school community.
     * Access is scoped to the user's jurisdictional boundaries.
     */
    public function view(Usuario $user, int $escuelaId): bool
    {
        // 1. Superusuario → acceso global sin restricciones
        if ($user->hasRole('superuser')) {
            return true;
        }

        // 2. Jefe Provincial → solo escuelas dentro de SU provincia
        if ($user->hasRole('jefe_provincial')) {
            $user->loadMissing('provinciaUsuario');
            if (!$user->provinciaUsuario) {
                return false;
            }
            $escuela = Escuela::with('localidad.departamento')->find($escuelaId);
            return $escuela?->localidad?->departamento?->provincia_id === $user->provinciaUsuario->provincia_id;
        }

        // 3. Jefe Regional → solo escuelas dentro de SU región
        if ($user->hasRole('jefe_regional')) {
            $user->loadMissing('regionUsuario');
            if (!$user->regionUsuario) {
                return false;
            }
            $escuela = Escuela::with('localidad.departamento')->find($escuelaId);
            return $escuela?->localidad?->departamento?->region_id === $user->regionUsuario->region_id;
        }

        // 4. Jefe Distrital → solo escuelas dentro de SU distrito (departamento)
        if ($user->hasRole('jefe_distrital')) {
            $user->loadMissing('distritoUsuario');
            if (!$user->distritoUsuario) {
                return false;
            }
            $escuela = Escuela::with('localidad')->find($escuelaId);
            return $escuela?->localidad?->departamento_id === $user->distritoUsuario->departamento_id;
        }

        // 5. Equipo de Conducción → solo SU escuela (verificado)
        return $user->escuelaUsuarios()
            ->where('escuela_id', $escuelaId)
            ->whereHas('role', function ($q) {
                $q->whereIn('name', ['director', 'vicedirector', 'secretario', 'prosecretario']);
            })
            ->whereNotNull('verified_at')
            ->exists();
    }
}
