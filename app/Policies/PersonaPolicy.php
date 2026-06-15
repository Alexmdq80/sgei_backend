<?php

namespace App\Policies;

use App\Models\Persona;
use App\Models\Usuario;
use Illuminate\Auth\Access\Response;

class PersonaPolicy
{
    /**
     * Determine whether the user can manage the persona padron (CRUD Global).
     * Applied to: viewAny, create, view, update.
     */
    private function canManageGlobalPadron(Usuario $usuario): bool
    {
        // 1. Superusuario, Jefe Provincial, Regional, Distrital: Acceso Total al Padrón.
        if ($usuario->hasAnyRole(['superuser', 'jefe_provincial', 'jefe_regional', 'jefe_distrital'])) {
            return true;
        }

        // 2. Equipo de Conducción: Acceso Total al Padrón (CRUD) para sus escuelas.
        // Verificamos si tiene algún rol directivo en cualquier escuela vinculada.
        return $usuario->escuelaUsuarios()
            ->whereHas('role', function($q) {
                $q->whereIn('name', ['director', 'vicedirector', 'secretario', 'prosecretario']);
            })
            ->whereNotNull('verified_at')
            ->exists();
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
