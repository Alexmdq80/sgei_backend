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
     * Determina si el usuario puede gestionar un usuario específico según su jurisdicción.
     */
    public function manageScoped(Usuario $user, Usuario $model): bool
    {
        return $this->update($user, $model);
    }

    /**
     * Determine whether the user can view any models.
     * SEGÚN REGLA: Superusuario, Equipo de Conducción o Jerárquicos (Provincial/Regional/Distrital).
     */
    public function viewAny(Usuario $user): bool
    {
        // 1. Superusuario: Acceso total.
        if ($user->hasRole('superuser')) {
            return true;
        }

        // 2. Roles Jerárquicos: Acceso (filtrado por su jurisdicción en el controller).
        if ($user->hasAnyRole(['jefe_provincial', 'jefe_regional', 'jefe_distrital'])) {
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
     * SEGÚN REGLA: Superusuario o Jerárquicos dentro de su jurisdicción.
     */
    public function view(Usuario $user, Usuario $model): bool
    {
        if ($user->hasRole('superuser')) {
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

        // 2. Jefe Regional
        if ($user->hasRole('jefe_regional')) {
            $userRegId = $user->region_usuario?->region_id;
            if ($userRegId && $model->region_usuario?->region_id === $userRegId) return true;
            
            if ($userRegId && $model->escuela_usuarios()->whereHas('escuela.region', function($q) use ($userRegId) {
                $q->where('id', $userRegId);
            })->exists()) return true;
        }

        // 3. Jefe Distrital
        if ($user->hasRole('jefe_distrital')) {
            $userDistId = $user->distrito_usuario?->departamento_id;
            if ($userDistId && $model->distrito_usuario?->departamento_id === $userDistId) return true;

            if ($userDistId && $model->escuela_usuarios()->whereHas('escuela', function($q) use ($userDistId) {
                $q->where('localidad_departamento_id', $userDistId);
            })->exists()) return true;
        }

        // 4. Equipo de Conducción
        if ($user->hasAnyRole(['director', 'vicedirector', 'secretario', 'prosecretario'])) {
            $userEscuelas = $user->escuela_usuarios()->whereNotNull('verified_at')->pluck('escuela_id');
            $modelEscuelas = $model->escuela_usuarios()->pluck('escuela_id');
            
            return $userEscuelas->intersect($modelEscuelas)->isNotEmpty();
        }

        return $user->id === $model->id;
    }

    /**
     * Determine whether the user can create models.
     * SEGÚN REGLA: Sólo Superusuario (para evitar creación de cuentas fantasma fuera de padrón).
     */
    public function create(Usuario $user): bool
    {
        return $user->hasRole('superuser');
    }

    /**
     * Determine whether the user can update the model.
     * SEGÚN REGLA: Superusuario o Jerárquicos dentro de su jurisdicción.
     */
    public function update(Usuario $user, Usuario $model): bool
    {
        if ($user->hasRole('superuser')) {
            return true;
        }

        if ($user->id === $model->id) {
            return true;
        }

        // Delegar a la lógica de 'view' para validar jurisdicción
        return $this->view($user, $model);
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
