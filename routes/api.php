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
        return $request->hasSession() ? $request->session()->getId() : 'no-session';
    });

    // Catálogos Públicos
    Route::get('/documento-tipos', [DocumentoTipoController::class, 'index']);
    Route::get('/rol-escolares', [App\Http\Controllers\Api\V1\RolEscolarController::class, 'index']);
    Route::get('/escuelas', [EscuelaController::class, 'index']);
    Route::get('/niveles', [EscuelaController::class, 'niveles']);
    Route::get('/sectores', [EscuelaController::class, 'sectores']);
    Route::get('/cargos', [App\Http\Controllers\Api\V1\CargoController::class, 'index']);

    // Geografía (Catálogos)
    Route::get('/provincias', [GeografiaController::class, 'provincias']);
    Route::get('/departamentos', [GeografiaController::class, 'departamentos']);
    Route::get('/localidades', [GeografiaController::class, 'localidades']);

    // Rutas de Autenticación
    Route::prefix('auth')->group(function () {
        Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:login');
        Route::post('/register', [RegisterController::class, 'register']);
        Route::post('/refresh', [LoginController::class, 'refresh']);
        Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->middleware('throttle:forgot-password');
        
        // Esta ruta existe solo para que Laravel no de error al generar notificaciones (Route [password.reset] not defined)
        Route::get('/reset-password', function () {
            return redirect(env('FRONTEND_URL', 'http://localhost:5173') . '/reset-password?' . http_build_query(request()->all()));
        })->name('password.reset');

        Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('api.password.reset');
        
        // Verificación de Email (Pública)
        Route::get('/verify', [VerificationController::class, 'verify']);

        // Rutas protegidas por Sanctum
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [LoginController::class, 'logout']);
            Route::get('/me', [ProfileController::class, 'me']);

            // Reenvío de Verificación (Permitido sin verificar email obviamente)
            Route::post('/verify/resend', [VerificationController::class, 'resend'])->middleware('throttle:resend-verification');

            // --- RUTAS QUE REQUIEREN VERIFICACIÓN ---
            Route::middleware('verified')->group(function () {
                // Perfil de Usuario
                Route::put('/profile', [ProfileController::class, 'update']);
                Route::post('/avatar', [ProfileController::class, 'updateAvatar']);
                Route::delete('/avatar', [ProfileController::class, 'deleteAvatar']);
                Route::put('/password', [ProfileController::class, 'updatePassword']);
            });
        });
    });

    // Gestión Académica (Planes de Estudio)
    Route::middleware(['auth:sanctum', 'verified'])->group(function () {
        Route::apiResource('planes', App\Http\Controllers\Api\V1\PlanController::class);
        
        // Gestión de Asignaturas
        Route::get('anio-plan/{id}/asignaturas', [App\Http\Controllers\Api\V1\AsignaturaController::class, 'indexByAnioPlan']);
        Route::apiResource('asignaturas', App\Http\Controllers\Api\V1\AsignaturaController::class)->except(['index']);

        // Gestión de Propuestas Institucionales
        Route::get('propuestas/escuelas-autorizadas', [App\Http\Controllers\Api\V1\PropuestaController::class, 'getAuthorizedSchools']);
        Route::apiResource('propuestas', App\Http\Controllers\Api\V1\PropuestaController::class);
        
        // Catálogos relacionados
        Route::get('/planes-ciclos', function() {
            return response()->json(\App\Models\PlanCiclo::all());
        });
        Route::get('/turnos', function() {
            return response()->json(\App\Models\Turno::all());
        });
        Route::get('/jornadas', function() {
            return response()->json(\App\Models\Jornada::all());
        });
        Route::get('/lectivos', [App\Http\Controllers\Api\V1\LectivoController::class, 'index']);
        Route::get('/anio-planes', function() {
            return response()->json(\App\Models\AnioPlan::with(['plan', 'anio'])->get());
        });
    });

    // Gestión Administrativa
    Route::middleware(['auth:sanctum', 'verified', 'permission:sistema.usuarios'])->prefix('admin')->group(function () {
        Route::apiResource('usuarios', App\Http\Controllers\Api\V1\UsuarioController::class);
        Route::post('/usuarios/{usuario}/confirm-persona', [App\Http\Controllers\Api\V1\UsuarioController::class, 'confirmPersona']);
        Route::post('/usuarios/{usuario}/toggle-supervisor', [App\Http\Controllers\Api\V1\UsuarioController::class, 'toggleSupervisorRole']);
        Route::post('/usuarios/{usuario}/toggle-jefe-distrital', [App\Http\Controllers\Api\V1\UsuarioController::class, 'toggleJefeDistritalRole']);
        
        // Gestión de Vinculaciones Escolares
        Route::apiResource('escuela-usuarios', App\Http\Controllers\Api\V1\Admin\EscuelaUsuarioController::class);
        Route::apiResource('personas', App\Http\Controllers\Api\V1\Admin\PersonaController::class)->only(['index', 'show', 'store', 'update']);
        Route::post('personas/{persona}/link-user', [App\Http\Controllers\Api\V1\Admin\PersonaController::class, 'tryLinkUser']);
        Route::post('personas/{persona}/unlink-user', [App\Http\Controllers\Api\V1\Admin\PersonaController::class, 'unlinkUser']);

        // Gestión de CUPOF y Agentes
        Route::apiResource('cargos', App\Http\Controllers\Api\V1\CargoController::class)->except(['index']);
        Route::apiResource('agentes', App\Http\Controllers\Api\V1\AgenteController::class)->only(['index', 'store']);
        Route::apiResource('cupofs', App\Http\Controllers\Api\V1\CupofController::class);
        Route::apiResource('escuelas', App\Http\Controllers\Api\V1\Admin\EscuelaController::class);
        Route::post('cupofs/{cupof}/assign', [App\Http\Controllers\Api\V1\CupofController::class, 'assign']);
        Route::post('cupofs/{cupof}/release', [App\Http\Controllers\Api\V1\CupofController::class, 'release']);

        // Gestión de Ciclos Lectivos (Panel Maestro)
        Route::apiResource('lectivos', App\Http\Controllers\Api\V1\LectivoController::class)->except(['index']);
        Route::apiResource('anios', App\Http\Controllers\Api\V1\AnioController::class);
        Route::apiResource('ambitos', App\Http\Controllers\Api\V1\AmbitoController::class);
        Route::apiResource('cierre-causas', App\Http\Controllers\Api\V1\CierreCausaController::class);
        Route::apiResource('condiciones', App\Http\Controllers\Api\V1\CondicionController::class);
        Route::apiResource('dependencias', App\Http\Controllers\Api\V1\DependenciaController::class);
        Route::apiResource('escuela-tipos', App\Http\Controllers\Api\V1\EscuelaTipoController::class);
        Route::apiResource('niveles', App\Http\Controllers\Api\V1\NivelController::class);
        Route::apiResource('modalidades', App\Http\Controllers\Api\V1\ModalidadController::class);
        Route::apiResource('escuela-ubicaciones', App\Http\Controllers\Api\V1\EscuelaUbicacionController::class);
        Route::apiResource('modalidad-niveles', App\Http\Controllers\Api\V1\ModalidadNivelController::class);
        Route::apiResource('ofertas', App\Http\Controllers\Api\V1\OfertaController::class);
        Route::apiResource('documento-situacions', App\Http\Controllers\Api\V1\DocumentoSituacionController::class);
        Route::apiResource('documento-tipos', App\Http\Controllers\Api\V1\DocumentoTipoController::class);
        Route::apiResource('generos', App\Http\Controllers\Api\V1\GeneroController::class);
        Route::apiResource('sexos', App\Http\Controllers\Api\V1\SexoController::class);
        Route::apiResource('continentes', App\Http\Controllers\Api\V1\ContinenteController::class);
        Route::apiResource('naciones', App\Http\Controllers\Api\V1\NacionController::class);
        Route::apiResource('provincias', App\Http\Controllers\Api\V1\ProvinciaController::class);
        Route::apiResource('departamentos', App\Http\Controllers\Api\V1\DepartamentoController::class);
        Route::apiResource('localidades', App\Http\Controllers\Api\V1\LocalidadController::class);
    });
});
