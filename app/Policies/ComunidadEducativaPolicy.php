<?php

namespace App\Policies;

use App\Models\Usuario;
use Illuminate\Auth\Access\Response;

class ComunidadEducativaPolicy
{
    /**
     * Determine whether the user can view the school community.
     */
    public function view(Usuario $user, int $escuelaId): bool
    {
        // 1. Superusuarios y Jefaturas (Provincial, Regional, Distrital) tienen acceso global
        if ($user->hasAnyRole(['superuser', 'jefe_provincial', 'jefe_regional', 'jefe_distrital'])) {
            return true;
        }

        // 2. Equipo de Conducción tiene acceso a SU escuela
        return $user->escuelaUsuarios()
            ->where('escuela_id', $escuelaId)
            ->whereHas('role', function($q) {
                $q->whereIn('name', ['director', 'vicedirector', 'secretario', 'prosecretario']);
            })
            ->whereNotNull('verified_at')
            ->exists();
    }
}
