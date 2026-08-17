<?php

namespace App\Policies;

use App\Models\Escuela;
use App\Models\Usuario;
use App\Policies\Concerns\HasSuperUserAccess;

class ComunidadEducativaPolicy
{
    use HasSuperUserAccess;

    /**
     * Determina si el usuario puede ver la comunidad educativa de una escuela.
     * El acceso está delimitado por las fronteras jurisdiccionales del usuario.
     * Superuser autorizado automáticamente por before().
     */
    public function view(Usuario $user, Escuela|int $escuela): bool
    {
        $escuelaId = $escuela instanceof Escuela ? $escuela->id : $escuela;

        // 1. Jefe Provincial → solo escuelas dentro de SU provincia
        if ($user->hasRole('jefe_provincial')) {
            $user->loadMissing('provinciaUsuario');
            if (!$user->provinciaUsuario) {
                return false;
            }

            $escuelaModel = $escuela instanceof Escuela ? $escuela : Escuela::with('localidad.departamento')->find($escuelaId);
            return $escuelaModel?->localidad?->departamento?->provincia_id === $user->provinciaUsuario->provincia_id;
        }

        // 2. Jefe Regional → solo escuelas dentro de SU región
        if ($user->hasRole('jefe_regional')) {
            $user->loadMissing('regionUsuario');
            if (!$user->regionUsuario) {
                return false;
            }

            $escuelaModel = $escuela instanceof Escuela ? $escuela : Escuela::with('localidad.departamento')->find($escuelaId);
            return $escuelaModel?->localidad?->departamento?->region_id === $user->regionUsuario->region_id;
        }

        // 3. Jefe Distrital → solo escuelas dentro de SU distrito (departamento)
        if ($user->hasRole('jefe_distrital')) {
            $user->loadMissing('distritoUsuario');
            if (!$user->distritoUsuario) {
                return false;
            }

            $escuelaModel = $escuela instanceof Escuela ? $escuela : Escuela::with('localidad')->find($escuelaId);
            return $escuelaModel?->localidad?->departamento_id === $user->distritoUsuario->departamento_id;
        }

        // 4. Equipo de Conducción → solo SU escuela (verificado)
        $rolesConduccion = ['director', 'vicedirector', 'secretario', 'prosecretario'];

        return $user->persona?->escuelasPersonas()
            ->where('escuela_id', $escuelaId)
            ->whereHas('role', fn($q) => $q->whereIn('name', $rolesConduccion))
            ->whereNotNull('verified_at')
            ->exists() ?? false;
    }
}