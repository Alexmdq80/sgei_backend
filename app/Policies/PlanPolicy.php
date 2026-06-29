<?php

namespace App\Policies;

use App\Models\Plan;
use App\Models\Usuario;
use Illuminate\Auth\Access\Response;

class PlanPolicy
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

        return $user->escuelaUsuarios()
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
        return $user->can('planes.ver') || $user->can('planes.gestionar');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Usuario $user, Plan $plan): bool
    {
        if ($this->isReadOnlyRole($user)) {
            return true;
        }
        return $user->can('planes.ver') || $user->can('planes.gestionar');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Usuario $user): bool
    {
        if ($this->isReadOnlyRole($user)) {
            return false;
        }
        return $user->hasPermissionTo('planes.crear');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Usuario $user, Plan $plan): bool
    {
        if ($this->isReadOnlyRole($user)) {
            return false;
        }
        return $user->hasPermissionTo('planes.editar');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Usuario $user, Plan $plan): bool
    {
        if ($this->isReadOnlyRole($user)) {
            return false;
        }
        return $user->hasPermissionTo('planes.eliminar');
    }

}
