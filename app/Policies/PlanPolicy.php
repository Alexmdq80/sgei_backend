<?php

namespace App\Policies;

use App\Models\Plan;
use App\Models\Usuario;
use App\Policies\Concerns\HasSuperUserAccess;

class PlanPolicy
{
    use HasSuperUserAccess;

    private function isReadOnlyRole(Usuario $user): bool
    {
        $restrictedRoles = [
            'director',
            'vicedirector',
            'secretario',
            'prosecretario'
        ];

        if ($user->hasAnyRole($restrictedRoles)) {
            return true;
        }

        // Si ya está cargada la relación en memoria, evitamos una query extra
        if ($user->relationLoaded('persona') && $user->persona?->relationLoaded('escuelasPersonas')) {
            return $user->persona->escuelasPersonas
                ->whereNotNull('verified_at')
                ->contains(fn($ep) => in_array($ep->role?->name, $restrictedRoles));
        }

        return $user->persona?->escuelasPersonas()
            ->whereNotNull('verified_at')
            ->whereHas('role', fn($q) => $q->whereIn('name', $restrictedRoles))
            ->exists() ?? false;
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

        return $user->can('planes.crear');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Usuario $user, Plan $plan): bool
    {
        if ($this->isReadOnlyRole($user)) {
            return false;
        }

        return $user->can('planes.editar');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Usuario $user, Plan $plan): bool
    {
        if ($this->isReadOnlyRole($user)) {
            return false;
        }

        return $user->can('planes.eliminar');
    }
}