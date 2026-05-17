<?php

namespace App\Policies;

use App\Models\Usuario;
use Illuminate\Auth\Access\Response;

class UsuarioPolicy
{
    /**
     * Determina si el usuario puede gestionar usuarios globalmente.
     * SEGÚN REGLA: Sólo Superusuario.
     */
    public function manageGlobal(Usuario $user): bool
    {
        return $user->hasRole('superuser');
    }

    /**
     * Determine whether the user can view any models.
     * SEGÚN REGLA: Superusuario o Equipo de Conducción.
     */
    public function viewAny(Usuario $user): bool
    {
        // 1. Superusuario: Acceso total.
        if ($user->hasRole('superuser')) {
            return true;
        }

        // 2. Equipo de Conducción: Acceso (filtrado por su colegio en el controller/service).
        if ($user->hasAnyRole(['director', 'vicedirector', 'secretario', 'prosecretario'])) {
            return true;
        }

        // Jefe Provincial, Regional, Distrital, Supervisor y otros: SIN ACCESO.
        return false;
    }

    /**
     * Determine whether the user can view the model.
     * SEGÚN REGLA: Superusuario o Conducción (si es de su colegio).
     */
    public function view(Usuario $user, Usuario $model): bool
    {
        if ($user->hasRole('superuser')) {
            return true;
        }

        if ($user->hasAnyRole(['director', 'vicedirector', 'secretario', 'prosecretario'])) {
            // Verificar si el usuario consultado tiene vinculación con el colegio del performer
            $userEscuelas = $user->escuela_usuarios()->whereNotNull('verified_at')->pluck('escuela_id');
            $modelEscuelas = $model->escuela_usuarios()->pluck('escuela_id');
            
            return $userEscuelas->intersect($modelEscuelas)->isNotEmpty();
        }

        return $user->id === $model->id;
    }

    /**
     * Determine whether the user can create models.
     * SEGÚN REGLA: Sólo Superusuario.
     */
    public function create(Usuario $user): bool
    {
        return $user->hasRole('superuser');
    }

    /**
     * Determine whether the user can update the model.
     * SEGÚN REGLA: Sólo Superusuario (o uno mismo para su propio perfil).
     */
    public function update(Usuario $user, Usuario $model): bool
    {
        if ($user->hasRole('superuser')) {
            return true;
        }

        return $user->id === $model->id;
    }

    /**
     * Determine whether the user can delete the model.
     * SEGÚN REGLA: Superusuario global, o Conducción (sólo desvincular de su colegio).
     * Nota: 'delete' aquí se refiere a la desvinculación o borrado administrativo.
     */
    public function delete(Usuario $user, Usuario $model): bool
    {
        if ($user->hasRole('superuser')) {
            return true;
        }

        // El Equipo de Conducción puede "desvincular" (borrado administrativo si se interpreta así) 
        // pero en este sistema la desvinculación institucional se maneja en EscuelaUsuarioController.
        // Si el usuario se refiere a borrar la CUENTA, sólo Superusuario.
        return false;
    }

    /**
     * Determine whether the user can desvincular institucionalmente.
     * SEGÚN REGLA: Equipo de Conducción para su colegio.
     */
    public function desvincular(Usuario $user, Usuario $model): bool
    {
        if ($user->hasRole('superuser')) {
            return true;
        }

        if ($user->hasAnyRole(['director', 'vicedirector', 'secretario', 'prosecretario'])) {
            $userEscuelas = $user->escuela_usuarios()->whereNotNull('verified_at')->pluck('escuela_id');
            $modelEscuelas = $model->escuela_usuarios()->pluck('escuela_id');
            
            return $userEscuelas->intersect($modelEscuelas)->isNotEmpty();
        }

        return false;
    }
}
