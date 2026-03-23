<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\ProfileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    // Rutas de Autenticación
    Route::prefix('auth')->group(function () {
        Route::post('/login', [LoginController::class, 'login']);
        
        // Rutas protegidas por Sanctum
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [LoginController::class, 'logout']);
            Route::get('/me', [ProfileController::class, 'me']);
            
            // Perfil de Usuario
            Route::put('/profile', [ProfileController::class, 'update']);
            Route::post('/avatar', [ProfileController::class, 'updateAvatar']);
            Route::put('/password', [ProfileController::class, 'updatePassword']);
        });
    });
    // Gestión de Usuarios (Administrativa) - Fuera del grupo 'auth'
    Route::middleware(['auth:sanctum', 'permission:manage-users'])->group(function () {
        Route::apiResource('usuarios', App\Http\Controllers\Api\V1\UsuarioController::class);
    });
});
