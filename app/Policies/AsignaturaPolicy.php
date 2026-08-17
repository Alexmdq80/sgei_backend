<?php

namespace App\Policies;

use App\Models\Asignatura;
use App\Models\Usuario;
use App\Policies\Concerns\HasSuperUserAccess;

class AsignaturaPolicy
{
    use HasSuperUserAccess;

    /**
     * Determina si el usuario posee un rol institucional restringido a sólo lectura.
     */
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

        // Si la relación ya está en memoria, evaluamos en la colección sin hacer queries a la BD
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
     * Superuser autorizado automáticamente por before().
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
     * Superuser autorizado automáticamente por before().
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
     * Superuser autorizado automáticamente por before().
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
     * Superuser autorizado automáticamente por before().
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
     * Superuser autorizado automáticamente por before().
     */
    public function delete(Usuario $user, Asignatura $asignatura): bool
    {
        if ($this->isReadOnlyRole($user)) {
            return false;
        }

        return $user->can('asignaturas.gestionar');
    }
}