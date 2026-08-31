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
        return false; // Si no es superuser (que ya entró por before), nadie más puede.
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Usuario $user, Usuario $model): bool
    {
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
        return false;
    }

    /**
     * Determina si el usuario puede buscar candidatos del padrón para vincular.
     */
    public function manageCandidatos(Usuario $user, Usuario $model): bool
    {
        return false;
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

        if (!$persona->vive_si) {
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

        return false;
    }
    /**
     * Verifica si una persona está bajo la jurisdicción del performer.
     */
    private function personaEnJurisdiccion(Persona $persona, Usuario $performer): bool
    {
        return false;
    }
    /**
     * Determine whether the user can view a target user's avatar.
     */
    public function viewAvatar(Usuario $user, Usuario $targetUser): bool
    {
        // 1. El propio usuario siempre puede ver su avatar
        if ($user->id === $targetUser->id) {
            return true;
        }

        // 2. Superusuario / Administrador
        if ($this->isSuperUser($user)) {
            return true;
        }

        // 3. Equipo de conducción de su misma escuela
        if ($user->hasAnyRole(Usuario::ROLES_EQUIPO_CONDUCCION)) {
            $userEscuelas = $user->persona?->escuelasPersonas()->whereNotNull('verified_at')->pluck('escuela_id');
            $modelEscuelas = $targetUser->persona?->escuelasPersonas()->pluck('escuela_id');

            return $userEscuelas && $modelEscuelas && $userEscuelas->intersect($modelEscuelas)->isNotEmpty();
        }

        return false;
    }


}