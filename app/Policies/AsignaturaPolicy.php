<?php

namespace App\Policies;

use App\Models\Asignatura;
use App\Models\Usuario;
use Illuminate\Auth\Access\Response;

class AsignaturaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Usuario $user): bool
    {
        return $user->can('asignaturas.ver') || $user->can('asignaturas.gestionar');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Usuario $user, Asignatura $asignatura): bool
    {
        return $user->can('asignaturas.ver') || $user->can('asignaturas.gestionar');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Usuario $user): bool
    {
        if ($user->hasRole('jefe_distrital')) {
            return false;
        }
        return $user->can('asignaturas.gestionar');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Usuario $user, Asignatura $asignatura): bool
    {
        if ($user->hasRole('jefe_distrital')) {
            return false;
        }
        return $user->can('asignaturas.gestionar');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Usuario $user, Asignatura $asignatura): bool
    {
        if ($user->hasRole('jefe_distrital')) {
            return false;
        }
        return $user->can('asignaturas.gestionar');
    }
}
