<?php

namespace App\Policies;

use App\Models\Plan;
use App\Models\Usuario;
use Illuminate\Auth\Access\Response;

class PlanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Usuario $user): bool
    {
        return $user->can('planes.ver') || $user->can('planes.gestionar');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Usuario $user, Plan $plan): bool
    {
        return $user->can('planes.ver') || $user->can('planes.gestionar');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Usuario $user): bool
    {
        return $user->can('planes.gestionar');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Usuario $user, Plan $plan): bool
    {
        return $user->can('planes.gestionar');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Usuario $user, Plan $plan): bool
    {
        return $user->can('planes.gestionar');
    }
}
