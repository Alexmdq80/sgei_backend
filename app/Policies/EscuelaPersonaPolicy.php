<?php

namespace App\Policies;

use App\Models\EscuelaPersona;
use App\Models\Usuario;
use App\Policies\Concerns\HasSuperUserAccess;

class EscuelaPersonaPolicy
{
    use HasSuperUserAccess;

    /**
     * Determine whether the user can view any models.
     * Superuser autorizado automáticamente por before().
     */
    public function viewAny(Usuario $user): bool
    {
        // 1. Jefaturas pueden listar vinculaciones
        if ($user->hasAnyRole(['jefe_provincial', 'jefe_regional', 'jefe_distrital'])) {
            return true;
        }

        // 2. Equipo de Conducción puede ver vinculaciones (para filtrar por su escuela)
        $rolesConduccion = ['director', 'vicedirector', 'secretario', 'prosecretario'];

        return $user->persona?->escuelasPersonas()
            ->whereHas('role', fn($q) => $q->whereIn('name', $rolesConduccion))
            ->whereNotNull('verified_at')
            ->exists() ?? false;
    }

    /**
     * Determine whether the user can view the model.
     * Superuser autorizado automáticamente por before().
     */
    public function view(Usuario $user, EscuelaPersona $escuelaPersona): bool
    {
        // 1. Jefaturas pueden ver la vinculación
        if ($user->hasAnyRole(['jefe_provincial', 'jefe_regional', 'jefe_distrital'])) {
            return true;
        }

        // 2. Equipo de Conducción solo si la vinculación pertenece a su escuela
        $rolesConduccion = ['director', 'vicedirector', 'secretario', 'prosecretario'];

        return $user->persona?->escuelasPersonas()
            ->where('escuela_id', $escuelaPersona->escuela_id)
            ->whereHas('role', fn($q) => $q->whereIn('name', $rolesConduccion))
            ->whereNotNull('verified_at')
            ->exists() ?? false;
    }
}