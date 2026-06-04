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
        return $user->hasRole('superuser') || $user->es_administrador;
    }

    /**
     * Determina si el usuario puede gestionar un usuario específico según su jurisdicción.
     */
    public function manageScoped(Usuario $user, Usuario $model): bool
    {
        return $this->update($user, $model);
    }

    /**
     * Determine whether the user can view any models.
     * SEGÚN REGLA: Superusuario, Equipo de Conducción o Jefe Provincial.
     */
    public function viewAny(Usuario $user): bool
    {
        // 1. Superusuario: Acceso total.
        if ($user->hasRole('superuser') || $user->es_administrador) {
            return true;
        }

        // 2. Jefe Provincial: Acceso (filtrado por su jurisdicción en el controller).
        if ($user->hasRole('jefe_provincial')) {
            return true;
        }

        // 3. Equipo de Conducción: Acceso (filtrado por su colegio en el controller).
        if ($user->hasAnyRole(['director', 'vicedirector', 'secretario', 'prosecretario'])) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     * SEGÚN REGLA: Superusuario, Jefe Provincial (misma provincia) o Conducción (mismo colegio).
     */
    public function view(Usuario $user, Usuario $model): bool
    {
        if ($user->hasRole('superuser') || $user->es_administrador) {
            return true;
        }

        // 1. Jefe Provincial
        if ($user->hasRole('jefe_provincial')) {
            $userProvId = $user->provincia_usuario?->provincia_id;
            $modelProvId = $model->provincia_usuario?->provincia_id;
            
            // Si el modelo tiene provincia asignada directamente o vía su escuela
            if ($userProvId && $modelProvId && $userProvId === $modelProvId) return true;

            // También ver si el usuario destino tiene escuelas en esa provincia
            if ($userProvId && $model->escuela_usuarios()->whereHas('escuela', function($q) use ($userProvId) {
                $q->where('provincia_id', $userProvId);
            })->exists()) return true;
        }

        // 2. Equipo de Conducción
        if ($user->hasAnyRole(['director', 'vicedirector', 'secretario', 'prosecretario'])) {
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
        return $user->hasRole('superuser') || $user->es_administrador;
    }

    /**
     * Determine whether the user can update the model.
     * SEGÚN REGLA: Superusuario, el propio usuario o Jefe Provincial de la misma provincia.
     */
    public function update(Usuario $user, Usuario $model): bool
    {
        if ($user->hasRole('superuser') || $user->es_administrador) {
            return true;
        }

        if ($user->id === $model->id) {
            return true;
        }

        // El Jefe Provincial puede actualizar usuarios de su provincia
        if ($user->hasRole('jefe_provincial')) {
            return $this->view($user, $model);
        }

        // El Equipo de Conducción tiene acceso sólo lectura (no pueden actualizar)
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     * SEGÚN REGLA: Sólo Superusuario.
     */
    public function delete(Usuario $user, Usuario $model): bool
    {
        return $user->hasRole('superuser') || $user->es_administrador;
    }

    /**
     * Determine whether the user can desvincular institucionalmente.
     * SEGÚN REGLA: Equipo de Conducción para su colegio, o Superusuario.
     */
    public function desvincular(Usuario $user, Usuario $model): bool
    {
        if ($user->hasRole('superuser') || $user->es_administrador) {
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
