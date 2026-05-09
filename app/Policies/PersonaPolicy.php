<?php

namespace App\Policies;

use App\Models\Persona;
use App\Models\Usuario;
use Illuminate\Auth\Access\Response;

class PersonaPolicy
{
    /**
     * Determine whether the user can manage the persona padron (CRUD Global).
     * Applied to: viewAny, create, view, update, delete.
     */
    private function canManageGlobalPadron(Usuario $usuario): bool
    {
        // 1. Superusuario: Acceso Total
        if ($usuario->hasRole('superuser')) {
            return true;
        }

        // 2. Roles Jerárquicos Administrativos: Acceso Total al Padrón
        if ($usuario->hasAnyRole(['jefe_provincial', 'jefe_regional', 'jefe_distrital'])) {
            return true;
        }

        // 3. Equipo de Conducción: Acceso Total al Padrón (para facilitar vinculaciones)
        $isConduccion = $usuario->hasAnyRole(['director', 'vicedirector', 'secretario', 'prosecretario']);
        if ($isConduccion) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Usuario $usuario): bool
    {
        return $this->canManageGlobalPadron($usuario);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Usuario $usuario, Persona $persona): bool
    {
        return $this->canManageGlobalPadron($usuario);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Usuario $usuario): bool
    {
        return $this->canManageGlobalPadron($usuario);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Usuario $usuario, Persona $persona): bool
    {
        return $this->canManageGlobalPadron($usuario);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Usuario $usuario, Persona $persona): bool
    {
        // Solo Superusuario puede borrar personas (Soft Delete)
        return $usuario->hasRole('superuser');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Usuario $usuario, Persona $persona): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Usuario $usuario, Persona $persona): bool
    {
        return false;
    }
}
