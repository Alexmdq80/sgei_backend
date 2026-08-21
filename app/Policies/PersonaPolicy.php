<?php

namespace App\Policies;

use App\Models\Persona;
use App\Models\Usuario;
use App\Policies\Concerns\HasSuperUserAccess;

class PersonaPolicy
{
    use HasSuperUserAccess;

    /**
     * Determine whether the user can view any models.
     * Superuser autorizado por before(). Jefaturas tienen acceso de lectura.
     */
    public function viewAny(Usuario $usuario): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Usuario $usuario, Persona $persona): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     * Superuser autorizado por before(). Jefaturas y Equipo de Conducción pueden crear.
     */
    public function create(Usuario $usuario): bool
    {

        $rolesConduccion = ['director', 'vicedirector', 'secretario', 'prosecretario'];

        return $usuario->persona?->escuelasPersonas()
            ->whereHas('role', fn($q) => $q->whereIn('name', $rolesConduccion))
            ->whereNotNull('verified_at')
            ->exists() ?? false;
    }

    /**
     * Determine whether the user can update the model.
     * SEGÚN REGLA: Sólo Superusuario (manejado por before()).
     */
    public function update(Usuario $usuario, Persona $persona): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     * SEGÚN REGLA: Sólo Superusuario (manejado por before()).
     */
    public function delete(Usuario $usuario, Persona $persona): bool
    {
        return false;
    }

    /**
     * Determine whether the user can assign roles.
     * Superuser autorizado por before(). Jefaturas pueden abrir el modal.
     */
    public function assignRoles(Usuario $usuario): bool
    {
        return false;
    }
}