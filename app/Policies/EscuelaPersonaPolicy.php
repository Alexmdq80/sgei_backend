<?php

namespace App\Policies;

use App\Models\EscuelaPersona;
use App\Models\Usuario;
use Illuminate\Auth\Access\Response;

class EscuelaPersonaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Usuario $user): bool
    {
        // 1. Superusuarios y Jefaturas pueden ver todo
        if ($user->hasAnyRole(['superuser', 'jefe_provincial', 'jefe_regional', 'jefe_distrital'])) {
            return true;
        }

        // 2. Equipo de Conducción puede ver vinculaciones (para filtrar por su escuela)
        return $user->persona?->escuelasPersonas()
            ->whereHas('role', function($q) {
                $q->whereIn('name', ['director', 'vicedirector', 'secretario', 'prosecretario']);
            })
            ->whereNotNull('verified_at')
            ->exists() ?? false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Usuario $user, EscuelaPersona $escuelaPersona): bool
    {
        if ($user->hasAnyRole(['superuser', 'jefe_provincial', 'jefe_regional', 'jefe_distrital'])) {
            return true;
        }

        return $user->persona?->escuelasPersonas()
            ->where('escuela_id', $escuelaPersona->escuela_id)
            ->whereHas('role', function($q) {
                $q->whereIn('name', ['director', 'vicedirector', 'secretario', 'prosecretario']);
            })
            ->whereNotNull('verified_at')
            ->exists() ?? false;
    }
}
