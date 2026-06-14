<?php

namespace App\Policies;

use App\Models\Usuario;
use App\Models\Persona;
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
     * SEGÚN REGLA: Superusuario, Equipo de Conducción o Jefaturas (Provincial, Regional, Distrital).
     */
    public function viewAny(Usuario $user): bool
    {
        // 1. Superusuario: Acceso total.
        if ($user->hasRole('superuser') || $user->es_administrador) {
            return true;
        }

        // 2. Jefaturas Jerárquicas: Acceso (filtrado por su jurisdicción en el controller/service).
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
     */
    public function view(Usuario $user, Usuario $model): bool
    {
        if ($user->hasRole('superuser') || $user->es_administrador) {
            return true;
        }

        // 1. Jefe Provincial
        if ($user->hasRole('jefe_provincial')) {
            $userProvId = $user->provinciaUsuario?->provincia_id;
            if (!$userProvId) return false;

            // Directo, vía región, vía distrito o vía escuela
            $isInJurisdiction = $model->provinciaUsuario?->provincia_id === $userProvId ||
                   $model->regionUsuario?->region?->provincia_id === $userProvId ||
                   $model->distritoUsuario?->distrito?->provincia_id === $userProvId ||
                   $model->escuelaUsuarios()->whereHas('escuela.localidad.departamento', function($q) use ($userProvId) {
                       $q->where('provincia_id', $userProvId);
                   })->exists();

            if ($isInJurisdiction) return true;

            // Check matching persona for pending vinculation
            if ($model->estado === 'vinculacion_pendiente') {
                return Persona::where('documento_tipo_id', $model->documento_tipo_id)
                    ->where('documento_numero', $model->documento_numero)
                    ->whereHas('contacto', function ($q) use ($model) {
                        $q->where('email', $model->email);
                    })
                    ->whereHas('movimientosCupofActivos.cupof.escuela.localidad.departamento', function ($q) use ($userProvId) {
                        $q->where('provincia_id', $userProvId);
                    })->exists();
            }

            return false;
        }

        // 2. Jefe Regional
        if ($user->hasRole('jefe_regional')) {
            $userRegId = $user->regionUsuario?->region_id;
            if (!$userRegId) return false;

            $isInJurisdiction = $model->regionUsuario?->region_id === $userRegId ||
                   $model->distritoUsuario?->distrito?->region_id === $userRegId ||
                   $model->escuelaUsuarios()->whereHas('escuela.localidad.departamento', function($q) use ($userRegId) {
                       $q->where('region_id', $userRegId);
                   })->exists();

            if ($isInJurisdiction) return true;

            // Check matching persona for pending vinculation
            if ($model->estado === 'vinculacion_pendiente') {
                return Persona::where('documento_tipo_id', $model->documento_tipo_id)
                    ->where('documento_numero', $model->documento_numero)
                    ->whereHas('contacto', function ($q) use ($model) {
                        $q->where('email', $model->email);
                    })
                    ->whereHas('movimientosCupofActivos.cupof.escuela.localidad.departamento', function ($q) use ($userRegId) {
                        $q->where('region_id', $userRegId);
                    })->exists();
            }

            return false;
        }

        // 3. Jefe Distrital
        if ($user->hasRole('jefe_distrital')) {
            $userDistId = $user->distritoUsuario?->departamento_id;
            if (!$userDistId) return false;

            $isInJurisdiction = $model->distritoUsuario?->departamento_id === $userDistId ||
                   $model->escuelaUsuarios()->whereHas('escuela.localidad', function($q) use ($userDistId) {
                       $q->where('departamento_id', $userDistId);
                   })->exists();

            if ($isInJurisdiction) return true;

            // Check matching persona for pending vinculation
            if ($model->estado === 'vinculacion_pendiente') {
                return Persona::where('documento_tipo_id', $model->documento_tipo_id)
                    ->where('documento_numero', $model->documento_numero)
                    ->whereHas('contacto', function ($q) use ($model) {
                        $q->where('email', $model->email);
                    })
                    ->whereHas('movimientosCupofActivos.cupof.escuela.localidad', function ($q) use ($userDistId) {
                        $q->where('departamento_id', $userDistId);
                    })->exists();
            }

            return false;
        }

        // 4. Equipo de Conducción
        if ($user->hasAnyRole(['director', 'vicedirector', 'secretario', 'prosecretario'])) {
            $userEscuelas = $user->escuelaUsuarios()->whereNotNull('verified_at')->pluck('escuela_id');
            $modelEscuelas = $model->escuelaUsuarios()->pluck('escuela_id');
            
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
     * SEGÚN REGLA: Superusuario, el propio usuario o Jefaturas de la misma jurisdicción.
     */
    public function update(Usuario $user, Usuario $model): bool
    {
        if ($user->hasRole('superuser') || $user->es_administrador) {
            return true;
        }

        if ($user->id === $model->id) {
            return true;
        }

        // Jefaturas pueden actualizar usuarios de su jurisdicción
        if ($user->hasAnyRole(['jefe_provincial', 'jefe_regional', 'jefe_distrital'])) {
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
            $userEscuelas = $user->escuelaUsuarios()->whereNotNull('verified_at')->pluck('escuela_id');
            $modelEscuelas = $model->escuelaUsuarios()->pluck('escuela_id');
            
            return $userEscuelas->intersect($modelEscuelas)->isNotEmpty();
        }

        return false;
    }
}
