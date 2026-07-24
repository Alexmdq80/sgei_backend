<?php

namespace App\Policies;

use App\Models\Asignatura;
use App\Models\Usuario;
use Illuminate\Auth\Access\Response;

class AsignaturaPolicy
{
    private function isReadOnlyRole(Usuario $user): bool
    {
        $restrictedRoles = [
            'jefe_provincial',
            'jefe_regional',
            'jefe_distrital',
            'director',
            'vicedirector',
            'secretario',
            'prosecretario'
        ];
        
        if ($user->hasAnyRole($restrictedRoles)) {
            return true;
        }

        return $user->persona?->escuelasPersonas()
            ->whereNotNull('verified_at')
            ->whereHas('role', function($q) use ($restrictedRoles) {
                $q->whereIn('name', $restrictedRoles);
            })
            ->exists();
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Usuario $user): bool
    {
        if ($this->isReadOnlyRole($user)) {
            return true;
        }
        return $user->can('asignaturas.ver') || $user->can('asignaturas.gestionar');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Usuario $user, Asignatura $asignatura): bool
    {
        if ($this->isReadOnlyRole($user)) {
            return true;
        }
        return $user->can('asignaturas.ver') || $user->can('asignaturas.gestionar');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Usuario $user): bool
    {
        if ($this->isReadOnlyRole($user)) {
            return false;
        }
        return $user->can('asignaturas.gestionar');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Usuario $user, Asignatura $asignatura): bool
    {
        if ($this->isReadOnlyRole($user)) {
            return false;
        }
        return $user->can('asignaturas.gestionar');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Usuario $user, Asignatura $asignatura): bool
    {
        if ($this->isReadOnlyRole($user)) {
            return false;
        }
        return $user->can('asignaturas.gestionar');
    }
}
