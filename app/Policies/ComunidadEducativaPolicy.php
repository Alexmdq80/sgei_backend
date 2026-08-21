<?php

namespace App\Policies;

use App\Models\Escuela;
use App\Models\Usuario;
use App\Policies\Concerns\HasSuperUserAccess;

class ComunidadEducativaPolicy
{
    use HasSuperUserAccess;

    /**
     * Determina si el usuario puede ver la comunidad educativa de una escuela.
     * El acceso está delimitado por las fronteras jurisdiccionales del usuario.
     * Superuser autorizado automáticamente por before().
     */
    public function view(Usuario $user, Escuela|int $escuela): bool
    {
        $escuelaId = $escuela instanceof Escuela ? $escuela->id : $escuela;

        // 4. Equipo de Conducción → solo SU escuela (verificado)
        $rolesConduccion = ['director', 'vicedirector', 'secretario', 'prosecretario'];

        return $user->persona?->escuelasPersonas()
            ->where('escuela_id', $escuelaId)
            ->whereHas('role', fn($q) => $q->whereIn('name', $rolesConduccion))
            ->whereNotNull('verified_at')
            ->exists() ?? false;
    }
}