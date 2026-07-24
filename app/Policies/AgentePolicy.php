<?php

namespace App\Policies;

use App\Models\Agente;
use App\Models\Usuario;
use Illuminate\Auth\Access\Response;

class AgentePolicy
{
    /**
     * Determine whether the user can manage agents.
     */
    private function canManageAgents(Usuario $usuario): bool
    {
        // 1. Superusuarios y Jefaturas
        if ($usuario->hasAnyRole(['superuser', 'jefe_provincial', 'jefe_regional', 'jefe_distrital'])) {
            return true;
        }

        // 2. Equipo de Conducción
        return $usuario->persona?->escuelasPersonas()
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
        return $this->canManageAgents($usuario);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Usuario $usuario, Agente $agente): bool
    {
        return $this->canManageAgents($usuario);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Usuario $usuario): bool
    {
        return $this->canManageAgents($usuario);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Usuario $usuario, Agente $agente): bool
    {
        return $this->canManageAgents($usuario);
    }
}
