<?php

namespace App\Policies;

use App\Models\EscuelaUsuario;
use App\Models\Usuario;
use Illuminate\Auth\Access\Response;

class EscuelaUsuarioPolicy
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
        return $user->escuelaUsuarios()
            ->whereHas('role', function($q) {
                $q->whereIn('name', ['director', 'vicedirector', 'secretario', 'prosecretario']);
            })
            ->whereNotNull('verified_at')
            ->exists();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Usuario $user, EscuelaUsuario $escuelaUsuario): bool
    {
        if ($user->hasAnyRole(['superuser', 'jefe_provincial', 'jefe_regional', 'jefe_distrital'])) {
            return true;
        }

        return $user->escuelaUsuarios()
            ->where('escuela_id', $escuelaUsuario->escuela_id)
            ->whereHas('role', function($q) {
                $q->whereIn('name', ['director', 'vicedirector', 'secretario', 'prosecretario']);
            })
            ->whereNotNull('verified_at')
            ->exists();
    }
}
