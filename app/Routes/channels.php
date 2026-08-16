<?php

use App\Models\Usuario;
use Illuminate\Support\Facades\Broadcast;

/**
 * Canal privado para actualizaciones de la nómina de usuarios.
 * Solo accesible por administradores y usuarios con roles jerárquicos o de conducción.
 */
Broadcast::channel('usuarios', function (Usuario $user) {
    // 1. Superusuarios y Administradores globales
    if ($user->es_administrador || $user->hasRole('superuser')) {
        return true;
    }

    // 2. Jefaturas Territoriales
    if ($user->hasAnyRole(['jefe_provincial', 'jefe_regional', 'jefe_distrital'])) {
        return true;
    }

    // 3. Equipos directivos / conducción institucional
    if ($user->hasAnyRole(Usuario::ROLES_EQUIPO_CONDUCCION)) {
        return true;
    }

    return false;
});