<?php

namespace App\Policies;

use App\Models\Usuario;
use App\Models\Persona;
use App\Policies\Concerns\HasSuperUserAccess;

class UsuarioPolicy
{
    use HasSuperUserAccess;

    /**
     * Determina si el usuario puede gestionar usuarios globalmente.
     * SEGÚN REGLA: Sólo Superusuario (el Trait before() lo autoriza automáticamente).
     */
    public function manageGlobal(Usuario $user): bool
    {
        return false; // Si no es superuser (que ya entró por before), nadie más puede.
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
        // Superuser ya fue autorizado por el before()
        return $user->hasAnyRole(['jefe_provincial', 'jefe_regional', 'jefe_distrital']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Usuario $user, Usuario $model): bool
    {
        // 1. Jefe Provincial
        if ($user->hasRole('jefe_provincial')) {
            $userProvId = $user->provinciaUsuario?->provincia_id;
            if (!$userProvId)
                return false;

            $isInJurisdiction = $model->provinciaUsuario?->provincia_id === $userProvId ||
                $model->regionUsuario?->region?->provincia_id === $userProvId ||
                $model->distritoUsuario?->distrito?->provincia_id === $userProvId ||
                $model->persona?->escuelasPersonas()->whereHas('escuela.localidad.departamento', function ($q) use ($userProvId) {
                    $q->where('provincia_id', $userProvId);
                })->exists();

            if ($isInJurisdiction)
                return true;

            if ($model->estado === 'vinculacion_pendiente') {
                return Persona::where('documento_tipo_id', $model->documento_tipo_id)
                    ->where('documento_numero', $model->documento_numero)
                    ->whereHas('contacto', fn($q) => $q->where('email', $model->email))
                    ->inProvincia($userProvId)
                    ->exists();
            }

            return false;
        }

        // 2. Jefe Regional
        if ($user->hasRole('jefe_regional')) {
            $userRegId = $user->regionUsuario?->region_id;
            if (!$userRegId)
                return false;

            $isInJurisdiction = $model->regionUsuario?->region_id === $userRegId ||
                $model->distritoUsuario?->distrito?->region_id === $userRegId ||
                $model->persona?->escuelasPersonas()->whereHas('escuela.localidad.departamento', function ($q) use ($userRegId) {
                    $q->where('region_id', $userRegId);
                })->exists();

            if ($isInJurisdiction)
                return true;

            if ($model->estado === 'vinculacion_pendiente') {
                return Persona::where('documento_tipo_id', $model->documento_tipo_id)
                    ->where('documento_numero', $model->documento_numero)
                    ->whereHas('contacto', fn($q) => $q->where('email', $model->email))
                    ->inRegion($userRegId)
                    ->exists();
            }

            return false;
        }

        // 3. Jefe Distrital
        if ($user->hasRole('jefe_distrital')) {
            $userDistId = $user->distritoUsuario?->departamento_id;
            if (!$userDistId)
                return false;

            $isInJurisdiction = $model->distritoUsuario?->departamento_id === $userDistId ||
                $model->persona?->escuelasPersonas()->whereHas('escuela.localidad', function ($q) use ($userDistId) {
                    $q->where('departamento_id', $userDistId);
                })->exists();

            if ($isInJurisdiction)
                return true;

            if ($model->estado === 'vinculacion_pendiente') {
                return Persona::where('documento_tipo_id', $model->documento_tipo_id)
                    ->where('documento_numero', $model->documento_numero)
                    ->whereHas('contacto', fn($q) => $q->where('email', $model->email))
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
     * SEGÚN REGLA: Sólo Superusuario (manejado por before()).
     */
    public function create(Usuario $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Usuario $user, Usuario $model): bool
    {
        if ($user->id === $model->id) {
            return true;
        }

        // Jefaturas pueden actualizar usuarios de su jurisdicción
        if ($user->hasAnyRole(['jefe_provincial', 'jefe_regional', 'jefe_distrital'])) {
            return $this->view($user, $model);
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     * SEGÚN REGLA: Sólo Superusuario (manejado por before()).
     */
    public function delete(Usuario $user, Usuario $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can desvincular institucionalmente.
     */
    public function desvincular(Usuario $user, Usuario $model): bool
    {
        if ($user->hasAnyRole(Usuario::ROLES_EQUIPO_CONDUCCION)) {
            $userEscuelas = $user->persona?->escuelasPersonas()->whereNotNull('verified_at')->pluck('escuela_id');
            $modelEscuelas = $model->persona?->escuelasPersonas()->pluck('escuela_id');

            return $userEscuelas->intersect($modelEscuelas)->isNotEmpty();
        }

        return false;
    }

    /**
     * Determina si el usuario puede confirmar la vinculación de identidad de otro usuario.
     */
    public function confirmPersona(Usuario $user, Usuario $model): bool
    {
        if ($user->hasAnyRole(['jefe_provincial', 'jefe_regional', 'jefe_distrital'])) {
            return $this->view($user, $model);
        }

        return false;
    }

    /**
     * Determina si el usuario puede buscar candidatos del padrón para vincular.
     */
    public function manageCandidatos(Usuario $user, Usuario $model): bool
    {
        return $user->hasAnyRole(['jefe_provincial', 'jefe_regional', 'jefe_distrital']);
    }

    /**
     * Determina si el usuario puede vincular una persona candidata a un usuario.
     */
    /**
     * Determina si el usuario puede desvincular la persona de un usuario.
     */
    public function desvincularPersona(Usuario $user, Usuario $model): bool
    {
        // 1. REGLA DURA (Aplica para todos, incluso si quien ejecuta es Superusuario)
        if ($model->es_administrador || $model->hasRole('superuser')) {
            return false;
        }

        if (!$model->persona) {
            return false;
        }

        // 2. Si no es el usuario objetivo, el Superusuario SÍ tiene permiso
        if ($this->isSuperUser($user)) {
            return true;
        }

        // 3. Jefaturas
        if ($user->hasAnyRole(['jefe_provincial', 'jefe_regional', 'jefe_distrital'])) {
            return $this->view($user, $model);
        }

        return false;
    }

    /**
     * Determina si el usuario puede vincular una persona candidata a un usuario.
     */
    public function vincularPersona(Usuario $user, Usuario $model, Persona $persona): bool
    {
        // 1. REGLA DURA (Aplica para todos)
        if ($model->es_administrador || $model->hasRole('superuser')) {
            return false;
        }

        if ($model->persona || $persona->usuario_id) {
            return false;
        }

        $documentoNumeroRaw = $persona->getRawOriginal('documento_numero');
        $emailCoincide = $persona->contacto?->email === $model->email;
        $dniCoincide = $persona->documento_tipo_id == $model->documento_tipo_id
            && $documentoNumeroRaw == $model->documento_numero;

        if (!$emailCoincide || !$dniCoincide) {
            return false;
        }

        // 2. Si pasó las reglas de integridad, el Superusuario tiene permiso
        if ($this->isSuperUser($user)) {
            return true;
        }

        // 3. Jefaturas
        if ($user->hasAnyRole(['jefe_provincial', 'jefe_regional', 'jefe_distrital'])) {
            return $this->personaEnJurisdiccion($persona, $user);
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