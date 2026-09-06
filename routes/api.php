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
    Route::get('/rol-escolares', [App\Http\Controllers\Api\V1\RolEscolarController::class, 'index']);
    Route::get('/documento-tipos', [DocumentoTipoController::class, 'index']);
    Route::get('/escuelas', [EscuelaController::class, 'index']);
    Route::get('/niveles', [EscuelaController::class, 'niveles']);
    Route::get('/sectores', [EscuelaController::class, 'sectores']);

    // Geografía (Catálogos)
    Route::get('/provincias', [GeografiaController::class, 'provincias']);
    Route::get('/regiones', [GeografiaController::class, 'regiones']);
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
        Route::post('/complete-setup', [VerificationController::class, 'completeSetup']);
        Route::post('/resend-activation', [VerificationController::class, 'resendActivation'])->middleware('throttle:resend-verification');

        // Rutas protegidas por Sanctum
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [LoginController::class, 'logout']);
            Route::get('/me', [ProfileController::class, 'me']);

            // Solicitudes para unirse a Escuelas (Autoservicio)
            Route::post('/escuelas/join', [App\Http\Controllers\Api\V1\Auth\EscuelaJoinController::class, 'join']);
            Route::post('/escuelas/cancel-join', [App\Http\Controllers\Api\V1\Auth\EscuelaJoinController::class, 'cancelJoin']);

            // Reenvío de Verificación (Permitido sin verificar email obviamente)
            Route::post('/verify/resend', [VerificationController::class, 'resend'])->middleware('throttle:resend-verification');

            // --- RUTAS QUE REQUIEREN VERIFICACIÓN ---
            Route::middleware('verified')->group(function () {
                // Perfil de Usuario
                Route::put('/profile', [ProfileController::class, 'update']);
                Route::post('/avatar', [ProfileController::class, 'updateAvatar']);
                Route::delete('/avatar', [ProfileController::class, 'deleteAvatar']);
                Route::put('/password', [ProfileController::class, 'updatePassword']);
                Route::get('/avatar', [ProfileController::class, 'getAvatar']);
            });
        });
    });

    // Rutas Protegidas Generales (Fuera del prefijo 'admin')

    Route::middleware(['auth:sanctum', 'verified'])->group(function () {
        Route::get('/cargos', [App\Http\Controllers\Api\V1\CargoController::class, 'index']);
        Route::get('/usuarios/{usuario}/avatar', [App\Http\Controllers\Api\V1\UsuarioController::class, 'getAvatar']);

        Route::apiResource('planes', App\Http\Controllers\Api\V1\PlanController::class);

        // Gestión de Asignaturas
        Route::get('anio-plan/{id}/asignaturas', [App\Http\Controllers\Api\V1\AsignaturaController::class, 'indexByAnioPlan']);
        Route::apiResource('asignaturas', App\Http\Controllers\Api\V1\AsignaturaController::class)->except(['index']);

        // Gestión de Propuestas Institucionales
        Route::get('propuestas/escuelas-autorizadas', [App\Http\Controllers\Api\V1\PropuestaController::class, 'getAuthorizedSchools']);
        Route::apiResource('propuestas', App\Http\Controllers\Api\V1\PropuestaController::class);

        // Catálogos relacionados
        Route::get('/planes-ciclos', function () {
            return response()->json(\App\Models\PlanCiclo::all());
        });
        Route::get('/turnos', function () {
            return response()->json(\App\Models\Turno::all());
        });
        Route::get('/jornadas', function () {
            return response()->json(\App\Models\Jornada::all());
        });
        Route::get('/lectivos', [App\Http\Controllers\Api\V1\LectivoController::class, 'index']);
        Route::get('/anio-planes', function () {
            return response()->json(\App\Models\AnioPlan::with(['plan', 'anio'])->get());
        });
        Route::get('/escalafones', [App\Http\Controllers\Api\V1\EscalafonController::class, 'index']);
        Route::get('/puesto-tipos', [App\Http\Controllers\Api\V1\PuestoTipoController::class, 'index']);

    });

    // Gestión Administrativa
    Route::middleware(['auth:sanctum', 'verified', 'block_panel_general'])->prefix('admin')->group(function () {

        // --- RUTAS DE ACCESO INSTITUCIONAL (Accesibles por Directivos y Jefaturas) ---
        // Protegidas individualmente por Policies
        Route::apiResource('escuela-personas', App\Http\Controllers\Api\V1\Admin\EscuelaPersonaController::class);
        Route::apiResource('personas', App\Http\Controllers\Api\V1\Admin\PersonaController::class);
        Route::post('personas/{persona}/resend-activation', [App\Http\Controllers\Api\V1\Admin\PersonaController::class, 'resendActivation']);
        Route::post('personas/{persona}/link-user', [App\Http\Controllers\Api\V1\Admin\PersonaController::class, 'tryLinkUser']);
        Route::post('personas/{persona}/unlink-user', [App\Http\Controllers\Api\V1\Admin\PersonaController::class, 'unlinkUser']);
        Route::get('personas/{persona}/foto', [App\Http\Controllers\Api\V1\Admin\PersonaController::class, 'getFoto']);
        Route::post('personas/{persona}/foto', [App\Http\Controllers\Api\V1\Admin\PersonaController::class, 'uploadFoto']);
        Route::delete('personas/{persona}/foto', [App\Http\Controllers\Api\V1\Admin\PersonaController::class, 'deleteFoto']);
        Route::get('personas/{persona}/domicilio-contacto', [\App\Http\Controllers\Api\V1\Admin\PersonaController::class, 'getDomicilioContacto']);
        Route::put('personas/{persona}/domicilio-contacto', [\App\Http\Controllers\Api\V1\Admin\PersonaController::class, 'syncDomicilioContacto']);

        Route::apiResource('cupofs', App\Http\Controllers\Api\V1\CupofController::class);
        Route::apiResource('agentes', App\Http\Controllers\Api\V1\AgenteController::class)->only(['index', 'store']);
        Route::apiResource('escuelas', App\Http\Controllers\Api\V1\Admin\EscuelaController::class);
        Route::post('cupofs/{cupof}/assign', [App\Http\Controllers\Api\V1\CupofController::class, 'assign']);
        Route::post('cupofs/{cupof}/release', [App\Http\Controllers\Api\V1\CupofController::class, 'release']);

        Route::get('comunidad-educativa', [App\Http\Controllers\Api\V1\Admin\ComunidadEducativaController::class, 'index']);

        // --- RUTAS DE ADMINISTRACIÓN GLOBAL (Requieren permiso sistema.usuarios) ---
        Route::middleware('permission:sistema.usuarios')->group(function () {
            Route::post('/usuarios/{usuario}/confirm-persona', [App\Http\Controllers\Api\V1\UsuarioPersonaController::class, 'confirmPersona']);
            Route::get('/usuarios/{usuario}/candidatos-persona', [App\Http\Controllers\Api\V1\UsuarioPersonaController::class, 'candidatosPersona']);
            Route::post('/usuarios/{usuario}/vincular-persona/{persona}', [App\Http\Controllers\Api\V1\UsuarioPersonaController::class, 'vincularPersona']);
            Route::post('/usuarios/{usuario}/desvincular-persona', [App\Http\Controllers\Api\V1\UsuarioPersonaController::class, 'desvincularPersona']);
            Route::post('/usuarios/{usuario}/resend-activation', [App\Http\Controllers\Api\V1\UsuarioController::class, 'resendActivation']);
            Route::post('/usuarios/{usuario}/resend-verification', [App\Http\Controllers\Api\V1\UsuarioController::class, 'resendEmailVerification']);

            Route::apiResource('usuarios', App\Http\Controllers\Api\V1\UsuarioController::class)->except(['store']);

            Route::delete('personas/{persona}/roles/{role}', [App\Http\Controllers\Api\V1\Admin\PersonaController::class, 'removeRole']);

            // Catálogo de Cargos (CRUD de maestro, el index es público vía /api/v1/cargos)
            Route::apiResource('cargos', App\Http\Controllers\Api\V1\CargoController::class)->except(['index']);

            // Gestión de Ciclos Lectivos (Panel Maestro)
            Route::apiResource('lectivos', App\Http\Controllers\Api\V1\LectivoController::class)->except(['index']);
            Route::apiResource('anios', App\Http\Controllers\Api\V1\AnioController::class);
            Route::apiResource('ambitos', App\Http\Controllers\Api\V1\AmbitoController::class);
            Route::apiResource('cierre-causas', App\Http\Controllers\Api\V1\CierreCausaController::class);
            Route::apiResource('condiciones', App\Http\Controllers\Api\V1\CondicionController::class);
            Route::apiResource('vinculo-tipos', App\Http\Controllers\Api\V1\VinculoTipoController::class);
            Route::apiResource('vinculos', App\Http\Controllers\Api\V1\VinculoController::class);
            Route::apiResource('dependencias', App\Http\Controllers\Api\V1\DependenciaController::class);
            Route::apiResource('escuela-tipos', App\Http\Controllers\Api\V1\EscuelaTipoController::class);
            Route::apiResource('niveles', App\Http\Controllers\Api\V1\NivelController::class);
            Route::apiResource('modalidades', App\Http\Controllers\Api\V1\ModalidadController::class);
            Route::apiResource('jornadas', App\Http\Controllers\Api\V1\JornadaController::class);
            Route::apiResource('turnos', App\Http\Controllers\Api\V1\TurnoController::class);
            Route::apiResource('escalafones', App\Http\Controllers\Api\V1\EscalafonController::class);
            Route::apiResource('puesto-tipos', App\Http\Controllers\Api\V1\PuestoTipoController::class);
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
            Route::apiResource('regiones', App\Http\Controllers\Api\V1\RegionController::class);
            Route::apiResource('departamentos', App\Http\Controllers\Api\V1\DepartamentoController::class);
            Route::apiResource('municipios', App\Http\Controllers\Api\V1\MunicipioController::class);
            Route::apiResource('localidades', App\Http\Controllers\Api\V1\LocalidadController::class);
            Route::apiResource('localidad-censals', App\Http\Controllers\Api\V1\LocalidadCensalController::class);
            Route::apiResource('calles', App\Http\Controllers\Api\V1\CalleController::class);

            // Catálogos Georef
            Route::apiResource('georef-fuentes', App\Http\Controllers\Api\V1\GeorefFuenteController::class);
            Route::apiResource('georef-categorias', App\Http\Controllers\Api\V1\GeorefCategoriaController::class);
            Route::apiResource('georef-funcions', App\Http\Controllers\Api\V1\GeorefFuncionController::class);
        });
    });
});
