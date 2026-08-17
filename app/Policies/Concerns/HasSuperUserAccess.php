<?php

namespace App\Policies\Concerns;

use App\Models\Usuario;

trait HasSuperUserAccess
{
    /**
     * Interceptor previo para todas las políticas.
     * Si retorna true, la acción se aprueba sin evaluar el resto del método.
     */
    public function before(Usuario $user, string $ability): ?bool
    {
        // ⚠️ Excepciones: No hacer bypass automático si la acción tiene reglas de integridad sobre el modelo
        if (in_array($ability, ['vincularPersona', 'desvincularPersona'])) {
            return null; // Permite que Laravel ejecute la función completa en la Policy
        }
        if ($this->isSuperUser($user)) {
            return true;
        }

        return null; // Continúa con la evaluación normal de la policy
    }

    public function isSuperUser(Usuario $user): bool
    {
        return $user->hasRole('superuser') || (bool) $user->es_administrador;
    }
}