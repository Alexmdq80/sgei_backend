<?php

use Illuminate\Support\Facades\Broadcast;

// 🔒 Le indicamos a Laravel que use el guard 'sanctum' para verificar al usuario
Broadcast::channel('usuarios', function ($user) {
    // Si el usuario está autenticado, tiene permiso
    return !is_null($user);
}, ['guards' => ['sanctum', 'web']]); // 👈 ¡ESTE ES EL PARÁMETRO CLAVE!
