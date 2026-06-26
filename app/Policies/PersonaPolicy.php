<?php

namespace App\Policies;

use App\Models\Persona;
use App\Models\Usuario;
use Illuminate\Auth\Access\Response;

class PersonaPolicy
{
    /**
     * Determine whether the user can manage the persona padron (CRUD Global).
     * Applied to: create, update, delete.
     */
    private function canManageGlobalPadron(Usuario $usuario): bool
    {
        // Solo Superusuario/Administrador tiene acceso de escritura al padrón global.
        return $usuario->hasRole('superuser') || $usuario->es_administrador;
    }

    /**
     * Determine whether the user can search/read the persona padron.
     * Jefaturas tienen acceso de lectura para buscar personas al asignar roles.
     */
    private function canReadPadron(Usuario $usuario): bool
    {
        return $this->canManageGlobalPadron($usuario)
            || $usuario->hasAnyRole(['jefe_provincial', 'jefe_regional', 'jefe_distrital']);
    }

    /**
     * Determine whether the user can view any models.
     * Jefaturas pueden buscar personas para asignar roles (solo lectura).
     */
    public function viewAny(Usuario $usuario): bool
    {
        return $this->canReadPadron($usuario);
    }

    /**
     * Determine whether the user can view the model.
     * Jefaturas pueden ver el detalle de una persona para asignar roles.
     */
    public function view(Usuario $usuario, Persona $persona): bool
    {
        return $this->canReadPadron($usuario);
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
     * SEGÚN REGLA: Sólo Superusuario, Provincial, Regional, Distrital y Conducción.
     */
    public function delete(Usuario $usuario, Persona $persona): bool
    {
        return $this->canManageGlobalPadron($usuario);
    }

    /**
     * Determine whether the user can assign roles.
     * La lógica específica de jerarquía está en PersonaController,
     * pero este Gate general valida si el usuario puede siquiera abrir el modal.
     */
    public function assignRoles(Usuario $usuario): bool
    {
        return $usuario->hasAnyRole([
            'superuser', 
            'jefe_provincial', 
            'jefe_regional', 
            'jefe_distrital'
        ]);
    }
}
