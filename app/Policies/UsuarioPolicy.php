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

    public function viewAny(Usuario $user): bool
    {
        // Solo Superusuario y jefes jerárquicos tienen acceso a ver la nómina de usuarios.
        return $user->hasRole('superuser') 
            || $user->es_administrador 
            || $user->hasAnyRole(['jefe_provincial', 'jefe_regional', 'jefe_distrital']);
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
                   $model->persona?->escuelasPersonas()->whereHas('escuela.localidad.departamento', function($q) use ($userProvId) {
                       $q->where('provincia_id', $userProvId);
                   })->exists();

            if ($isInJurisdiction) return true;

            // Check matching persona for pending vinculation using scope
            if ($model->estado === 'vinculacion_pendiente') {
                return Persona::where('documento_tipo_id', $model->documento_tipo_id)
                    ->where('documento_numero', $model->documento_numero)
                    ->whereHas('contacto', fn ($q) => $q->where('email', $model->email))
                    ->inProvincia($userProvId)
                    ->exists();
            }

            return false;
        }

        // 2. Jefe Regional
        if ($user->hasRole('jefe_regional')) {
            $userRegId = $user->regionUsuario?->region_id;
            if (!$userRegId) return false;

            $isInJurisdiction = $model->regionUsuario?->region_id === $userRegId ||
                   $model->distritoUsuario?->distrito?->region_id === $userRegId ||
                   $model->persona?->escuelasPersonas()->whereHas('escuela.localidad.departamento', function($q) use ($userRegId) {
                       $q->where('region_id', $userRegId);
                   })->exists();

            if ($isInJurisdiction) return true;

            // Check matching persona for pending vinculation using scope
            if ($model->estado === 'vinculacion_pendiente') {
                return Persona::where('documento_tipo_id', $model->documento_tipo_id)
                    ->where('documento_numero', $model->documento_numero)
                    ->whereHas('contacto', fn ($q) => $q->where('email', $model->email))
                    ->inRegion($userRegId)
                    ->exists();
            }

            return false;
        }

        // 3. Jefe Distrital
        if ($user->hasRole('jefe_distrital')) {
            $userDistId = $user->distritoUsuario?->departamento_id;
            if (!$userDistId) return false;

            $isInJurisdiction = $model->distritoUsuario?->departamento_id === $userDistId ||
                   $model->persona?->escuelasPersonas()->whereHas('escuela.localidad', function($q) use ($userDistId) {
                       $q->where('departamento_id', $userDistId);
                   })->exists();

            if ($isInJurisdiction) return true;

            // Check matching persona for pending vinculation using scope
            if ($model->estado === 'vinculacion_pendiente') {
                return Persona::where('documento_tipo_id', $model->documento_tipo_id)
                    ->where('documento_numero', $model->documento_numero)
                    ->whereHas('contacto', fn ($q) => $q->where('email', $model->email))
                    ->inDepartamento($userDistId)
                    ->exists();
            }

            return false;
        }

        // 4. Equipo de Conducción
        if ($user->hasAnyRole(Usuario::ROLES_EQUIPO_CONDUCCION)) {
            $userEscuelas = $user->persona?->escuelasPersonas()->whereNotNull('verified_at')->pluck('escuela_id');
            $modelEscuelas = $model->persona?->escuelasPersonas()->pluck('escuela_id');

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

        if ($user->hasAnyRole(Usuario::ROLES_EQUIPO_CONDUCCION)) {
            $userEscuelas = $user->persona?->escuelasPersonas()->whereNotNull('verified_at')->pluck('escuela_id');
            $modelEscuelas = $model->persona?->escuelasPersonas()->pluck('escuela_id');

            return $userEscuelas->intersect($modelEscuelas)->isNotEmpty();
        }

        return false;
    }

    /**
     * Determina si el usuario puede confirmar la vinculación de identidad de otro usuario.
     * SEGÚN REGLA: Superusuario y Jefaturas Jerárquicas. El Equipo de Conducción NO.
     */
    public function confirmPersona(Usuario $user, Usuario $model): bool
    {
        if ($user->hasRole('superuser') || $user->es_administrador) {
            return true;
        }

        // Jefaturas pueden confirmar si el usuario está en su jurisdicción
        if ($user->hasAnyRole(['jefe_provincial', 'jefe_regional', 'jefe_distrital'])) {
            return $this->view($user, $model);
        }

        // El Equipo de Conducción NO puede confirmar vinculaciones de identidad
        return false;
    }

    /**
     * Determina si el usuario puede buscar candidatos del padrón para vincular.
     * SEGÚN REGLA: Superusuario y Jefaturas Jerárquicas.
     */
    public function manageCandidatos(Usuario $user, Usuario $model): bool
    {
        if ($user->hasRole('superuser') || $user->es_administrador) {
            return true;
        }

        return $user->hasAnyRole(['jefe_provincial', 'jefe_regional', 'jefe_distrital']);
    }

    /**
     * Determina si el usuario puede vincular una persona candidata a un usuario.
     * SEGÚN REGLA: Superusuario y Jefaturas, con verificación de identidad y jurisdicción.
     */
    public function vincularPersona(Usuario $user, Usuario $model, Persona $persona): bool
    {
        // No permitir vincular superusuarios/administradores
        if ($model->es_administrador || $model->hasRole('superuser')) {
            return false;
        }

        // No permitir si el usuario ya tiene persona vinculada
        if ($model->persona) {
            return false;
        }

        // No permitir si la persona ya está vinculada a otro usuario
        if ($persona->usuario_id) {
            return false;
        }

        // Verificar coincidencia de identidad (DNI + Email)
        $documentoNumeroRaw = $persona->getRawOriginal('documento_numero');
        $emailCoincide = $persona->contacto?->email === $model->email;
        $dniCoincide = $persona->documento_tipo_id == $model->documento_tipo_id
            && $documentoNumeroRaw == $model->documento_numero;

        if (!$emailCoincide || !$dniCoincide) {
            return false;
        }

        // Superuser/Admin pueden vincular cualquier persona
        if ($user->hasRole('superuser') || $user->es_administrador) {
            return true;
        }

        // Jefaturas: verificar que la persona esté en su jurisdicción
        if ($user->hasAnyRole(['jefe_provincial', 'jefe_regional', 'jefe_distrital'])) {
            return $this->personaEnJurisdiccion($persona, $user);
        }

        return false;
    }

    /**
     * Determina si el usuario puede desvincular la persona de un usuario.
     * SEGÚN REGLA: Superusuario y Jefaturas de la misma jurisdicción.
     */
    public function desvincularPersona(Usuario $user, Usuario $model): bool
    {
        // No permitir desvincular superusuarios/administradores
        if ($model->es_administrador || $model->hasRole('superuser')) {
            return false;
        }

        // No permitir si el usuario no tiene persona vinculada
        if (!$model->persona) {
            return false;
        }

        if ($user->hasRole('superuser') || $user->es_administrador) {
            return true;
        }

        // Jefaturas pueden desvincular si el usuario está en su jurisdicción
        if ($user->hasAnyRole(['jefe_provincial', 'jefe_regional', 'jefe_distrital'])) {
            return $this->view($user, $model);
        }

        return false;
    }
    /**
     * Verifica si una persona está bajo la jurisdicción del performer.
     */
    private function personaEnJurisdiccion(Persona $persona, Usuario $performer): bool
    {
        if ($performer->hasRole('jefe_provincial')) {
            $provId = $performer->provinciaUsuario?->provincia_id;
            return $provId && Persona::where('id', $persona->id)->inProvincia($provId)->exists();
        }

        if ($performer->hasRole('jefe_regional')) {
            $regId = $performer->regionUsuario?->region_id;
            return $regId && Persona::where('id', $persona->id)->inRegion($regId)->exists();
        }

        if ($performer->hasRole('jefe_distrital')) {
            $distId = $performer->distritoUsuario?->departamento_id;
            return $distId && Persona::where('id', $persona->id)->inDepartamento($distId)->exists();
        }

        return false;
    }

    
}
