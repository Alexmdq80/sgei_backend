<?php

namespace App\Policies;

use App\Models\Agente;
use App\Models\Usuario;
use App\Policies\Concerns\HasSuperUserAccess;

class AgentePolicy
{
    use HasSuperUserAccess;

    /**
     * Determina si el usuario tiene permisos generales para gestionar agentes.
     * Superuser autorizado automáticamente por before().
     */
    private function canManageAgents(Usuario $usuario): bool
    {
        // 2. Equipo de Conducción Escolar
        $rolesConduccion = ['director', 'vicedirector', 'secretario', 'prosecretario'];

        return $usuario->persona?->escuelasPersonas()
            ->whereHas('role', fn($q) => $q->whereIn('name', $rolesConduccion))
            ->whereNotNull('verified_at')
            ->exists() ?? false;
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