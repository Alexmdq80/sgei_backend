<?php

namespace App\Policies;

use App\Models\Escuela;
use App\Models\Usuario;
use Illuminate\Auth\Access\Response;

class EscuelaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Usuario $usuario): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Usuario $usuario, Escuela $escuela): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Usuario $usuario): bool
    {
        return $usuario->hasAnyRole(['superuser', 'jefe_provincial']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Usuario $usuario, Escuela $escuela): bool
    {
        // 1. Superuser y Jefe Provincial pueden todo
        if ($usuario->hasAnyRole(['superuser', 'jefe_provincial'])) {
            return true;
        }

        // 2. Jefe Regional: Solo escuelas en su región
        if ($usuario->hasRole('jefe_regional')) {
            $usuario->loadMissing('regionUsuario');
            return $usuario->regionUsuario && $escuela->localidad?->departamento?->region_id === $usuario->regionUsuario->region_id;
        }

        // 3. Jefe Distrital: Solo escuelas en su distrito
        if ($usuario->hasRole('jefe_distrital')) {
            $usuario->loadMissing('distritoUsuario');
            return $usuario->distritoUsuario && $escuela->localidad?->departamento_id === $usuario->distritoUsuario->departamento_id;
        }

        // 4. Equipo de Conducción: Solo su propia escuela (Autogestión)
        $isConduccionInThisSchool = $usuario->escuelaUsuarios()
            ->where('escuela_id', $escuela->id)
            ->whereHas('role', function($q) {
                $q->whereIn('name', ['director', 'vicedirector', 'secretario', 'prosecretario']);
            })
            ->whereNotNull('verified_at')
            ->exists();

        return $isConduccionInThisSchool;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Usuario $usuario, Escuela $escuela): bool
    {
        return $usuario->hasAnyRole(['superuser', 'jefe_provincial']);
    }
}
