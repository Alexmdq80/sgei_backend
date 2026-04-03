<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\ProfileController;
use App\Http\Controllers\Api\V1\Auth\VerificationController;
use App\Http\Controllers\Api\V1\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\V1\DocumentoTipoController;
use App\Http\Controllers\Api\V1\EscuelaController;
use App\Http\Controllers\Api\V1\GeografiaController;

use App\Http\Controllers\Api\V1\Auth\RegisterController;

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
    // RUTA DE PRUEBA
    Route::get('/check-id', function (Request $request) {
        return $request->session()->getId();
    });

    // Catálogos Públicos
    Route::get('/documento-tipos', [DocumentoTipoController::class, 'index']);
    Route::get('/escuelas', [EscuelaController::class, 'index']);
    Route::get('/niveles', [EscuelaController::class, 'niveles']);
    Route::get('/sectores', [EscuelaController::class, 'sectores']);

    // Geografía (Catálogos)
    Route::get('/provincias', [GeografiaController::class, 'provincias']);
    Route::get('/departamentos', [GeografiaController::class, 'departamentos']);
    Route::get('/localidades', [GeografiaController::class, 'localidades']);

    // Rutas de Autenticación
    Route::prefix('auth')->group(function () {
        Route::post('/login', [LoginController::class, 'login']);
        Route::post('/register', [RegisterController::class, 'register']);
        Route::post('/refresh', [LoginController::class, 'refresh']);
        Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->middleware('throttle:forgot-password');
        Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.reset');
        
        // Verificación de Email (Pública)
        Route::get('/verify', [VerificationController::class, 'verify']);

        // Rutas protegidas por Sanctum
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [LoginController::class, 'logout']);
            Route::get('/me', [ProfileController::class, 'me']);

            // Selección de Escuela (Post-Registro)
            Route::post('/escuelas/join', [EscuelaController::class, 'requestJoin']);
            Route::post('/escuelas/cancel-join', [EscuelaController::class, 'cancelJoin']);

            // Reenvío de Verificación
            Route::post('/verify/resend', [VerificationController::class, 'resend'])->middleware('throttle:resend-verification');
            
            // Perfil de Usuario
            Route::put('/profile', [ProfileController::class, 'update']);
            Route::post('/avatar', [ProfileController::class, 'updateAvatar']);
            Route::delete('/avatar', [ProfileController::class, 'deleteAvatar']);
            Route::put('/password', [ProfileController::class, 'updatePassword']);
        });
    });
    // Gestión Administrativa
    Route::middleware(['auth:sanctum', 'permission:sistema.usuarios'])->prefix('admin')->group(function () {
        Route::apiResource('usuarios', App\Http\Controllers\Api\V1\UsuarioController::class);
        
        // Gestión de Solicitudes de Unión a Escuela
        Route::get('/escuelas/pending', [App\Http\Controllers\Api\V1\Admin\EscuelaUsuarioController::class, 'indexPending']);
        Route::post('/escuelas/requests/{id}/approve', [App\Http\Controllers\Api\V1\Admin\EscuelaUsuarioController::class, 'approve']);
        Route::post('/escuelas/requests/{id}/reject', [App\Http\Controllers\Api\V1\Admin\EscuelaUsuarioController::class, 'reject']);
    });
});
