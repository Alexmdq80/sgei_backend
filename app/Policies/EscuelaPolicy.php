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
        return $usuario->hasRole('jefe_provincial');
    }

    /**
     * Determine whether the user can update the model.
     * Superuser autorizado por before().
     */
    public function update(Usuario $usuario, Escuela $escuela): bool
    {
        // 1. Jefe Provincial puede editar cualquier escuela
        if ($usuario->hasRole('jefe_provincial')) {
            return true;
        }

        // 2. Jefe Regional: Solo escuelas en su región
        if ($usuario->hasRole('jefe_regional')) {
            $usuario->loadMissing('regionUsuario');
            return $usuario->regionUsuario && $escuela->localidad?->departamento?->region_id === $usuario->regionUsuario->region_id;
        }

        // 3. Jefe Distrital: Solo escuelas en su distrito
        if ($usuario->hasRole('jefe_distrital')) {
            $usuario->loadMissing('distritoUsuario');
            return $usuario->distritoUsuario && $escuela->localidad?->departamento_id === $usuario->distritoUsuario->departamento_id;
        }

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
        return $usuario->hasRole('jefe_provincial');
    }
}