<?php

namespace App\Policies;

use App\Models\Escuela;
use App\Models\Usuario;
use App\Policies\Concerns\HasSuperUserAccess;

class EscuelaPolicy
{
    use HasSuperUserAccess;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Usuario $usuario): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Usuario $usuario, Escuela $escuela): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     * Superuser autorizado por before().
     */
    public function create(Usuario $usuario): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     * Superuser autorizado por before().
     */
    public function update(Usuario $usuario, Escuela $escuela): bool
    {
        // 4. Equipo de Conducción: Solo su propia escuela (Autogestión)
        $rolesConduccion = ['director', 'vicedirector', 'secretario', 'prosecretario'];

        return $usuario->persona?->escuelasPersonas()
            ->where('escuela_id', $escuela->id)
            ->whereHas('role', fn($q) => $q->whereIn('name', $rolesConduccion))
            ->whereNotNull('verified_at')
            ->exists() ?? false;
    }

    /**
     * Determine whether the user can delete the model.
     * Superuser autorizado por before().
     */
    public function delete(Usuario $usuario, Escuela $escuela): bool
    {
        return false;
    }
}