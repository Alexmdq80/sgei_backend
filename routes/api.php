<?php

use App\Http\Controllers\Api\V1\Admin\ComunidadEducativaController;
use App\Http\Controllers\Api\V1\Admin\EscuelaPersonaController;
use App\Http\Controllers\Api\V1\Admin\PersonaController;
use App\Http\Controllers\Api\V1\AgenteController;
use App\Http\Controllers\Api\V1\AmbitoController;
use App\Http\Controllers\Api\V1\AnioController;
use App\Http\Controllers\Api\V1\AsignaturaController;
use App\Http\Controllers\Api\V1\Auth\EscuelaJoinController;
use App\Http\Controllers\Api\V1\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\ProfileController;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use App\Http\Controllers\Api\V1\Auth\VerificationController;
use App\Http\Controllers\Api\V1\CalleController;
use App\Http\Controllers\Api\V1\CargoController;
use App\Http\Controllers\Api\V1\CierreCausaController;
use App\Http\Controllers\Api\V1\CondicionController;
use App\Http\Controllers\Api\V1\ContinenteController;
use App\Http\Controllers\Api\V1\CupofController;
use App\Http\Controllers\Api\V1\DepartamentoController;
use App\Http\Controllers\Api\V1\DependenciaController;
use App\Http\Controllers\Api\V1\DocumentoSituacionController;
use App\Http\Controllers\Api\V1\DocumentoTipoController;
use App\Http\Controllers\Api\V1\EscalafonController;
use App\Http\Controllers\Api\V1\EscuelaController;
use App\Http\Controllers\Api\V1\EscuelaTipoController;
use App\Http\Controllers\Api\V1\EscuelaUbicacionController;
use App\Http\Controllers\Api\V1\GeneroController;
use App\Http\Controllers\Api\V1\GeografiaController;
use App\Http\Controllers\Api\V1\GeorefCategoriaController;
use App\Http\Controllers\Api\V1\GeorefFuenteController;
use App\Http\Controllers\Api\V1\GeorefFuncionController;
use App\Http\Controllers\Api\V1\JornadaController;
use App\Http\Controllers\Api\V1\LectivoController;
use App\Http\Controllers\Api\V1\LocalidadCensalController;
use App\Http\Controllers\Api\V1\LocalidadController;
use App\Http\Controllers\Api\V1\ModalidadController;
use App\Http\Controllers\Api\V1\ModalidadNivelController;
use App\Http\Controllers\Api\V1\MunicipioController;
use App\Http\Controllers\Api\V1\NacionController;
use App\Http\Controllers\Api\V1\NivelController;
use App\Http\Controllers\Api\V1\OfertaController;
use App\Http\Controllers\Api\V1\PlanController;
use App\Http\Controllers\Api\V1\PropuestaController;
use App\Http\Controllers\Api\V1\ProvinciaController;
use App\Http\Controllers\Api\V1\PuestoTipoController;
use App\Http\Controllers\Api\V1\RegionController;
use App\Http\Controllers\Api\V1\RolEscolarController;
use App\Http\Controllers\Api\V1\SexoController;
use App\Http\Controllers\Api\V1\TurnoController;
use App\Http\Controllers\Api\V1\UsuarioController;
use App\Http\Controllers\Api\V1\UsuarioPersonaController;
use App\Http\Controllers\Api\V1\VinculoController;
use App\Http\Controllers\Api\V1\VinculoTipoController;
use App\Models\AnioPlan;
use App\Models\Jornada;
use App\Models\PlanCiclo;
use App\Models\Turno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
    Route::get('/rol-escolares', [RolEscolarController::class, 'index']);
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
            return redirect(env('FRONTEND_URL', 'http://localhost:5173').'/reset-password?'.http_build_query(request()->all()));
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
            Route::post('/escuelas/join', [EscuelaJoinController::class, 'join']);
            Route::post('/escuelas/cancel-join', [EscuelaJoinController::class, 'cancelJoin']);

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
        Route::get('/cargos', [CargoController::class, 'index']);
        Route::get('/usuarios/{usuario}/avatar', [UsuarioController::class, 'getAvatar']);

        Route::apiResource('planes', PlanController::class);

        // Gestión de Asignaturas
        Route::get('anio-plan/{id}/asignaturas', [AsignaturaController::class, 'indexByAnioPlan']);
        Route::apiResource('asignaturas', AsignaturaController::class)->except(['index']);

        // Gestión de Propuestas Institucionales
        Route::get('propuestas/escuelas-autorizadas', [PropuestaController::class, 'getAuthorizedSchools']);
        Route::apiResource('propuestas', PropuestaController::class);

        // Catálogos relacionados
        Route::get('/planes-ciclos', function () {
            return response()->json(PlanCiclo::all());
        });
        Route::get('/turnos', function () {
            return response()->json(Turno::all());
        });
        Route::get('/jornadas', function () {
            return response()->json(Jornada::all());
        });
        Route::get('/lectivos', [LectivoController::class, 'index']);
        Route::get('/anio-planes', function () {
            return response()->json(AnioPlan::with(['plan', 'anio'])->get());
        });
        Route::get('/escalafones', [EscalafonController::class, 'index']);
        Route::get('/puesto-tipos', [PuestoTipoController::class, 'index']);

    });

    // Gestión Administrativa
    Route::middleware(['auth:sanctum', 'verified', 'block_panel_general'])->prefix('admin')->group(function () {

        // --- RUTAS DE ACCESO INSTITUCIONAL (Accesibles por Directivos y Jefaturas) ---
        // Protegidas individualmente por Policies
        Route::apiResource('escuela-personas', EscuelaPersonaController::class);
        Route::apiResource('personas', PersonaController::class);
        Route::post('personas/{persona}/resend-activation', [PersonaController::class, 'resendActivation']);
        Route::post('personas/{persona}/link-user', [PersonaController::class, 'tryLinkUser']);
        Route::post('personas/{persona}/unlink-user', [PersonaController::class, 'unlinkUser']);
        Route::get('personas/{persona}/foto', [PersonaController::class, 'getFoto']);
        Route::post('personas/{persona}/foto', [PersonaController::class, 'uploadFoto']);
        Route::delete('personas/{persona}/foto', [PersonaController::class, 'deleteFoto']);
        Route::get('personas/{persona}/domicilio', [PersonaController::class, 'getDomicilio']);
        Route::put('personas/{persona}/domicilio', [PersonaController::class, 'syncDomicilio']);
        Route::get('personas/{persona}/contacto', [PersonaController::class, 'getContacto']);
        Route::put('personas/{persona}/contacto', [PersonaController::class, 'syncContacto']);

        Route::apiResource('cupofs', CupofController::class);
        Route::apiResource('agentes', AgenteController::class)->only(['index', 'store']);
        Route::apiResource('escuelas', App\Http\Controllers\Api\V1\Admin\EscuelaController::class);
        Route::post('cupofs/{cupof}/assign', [CupofController::class, 'assign']);
        Route::post('cupofs/{cupof}/release', [CupofController::class, 'release']);

        Route::get('comunidad-educativa', [ComunidadEducativaController::class, 'index']);

        // --- RUTAS DE ADMINISTRACIÓN GLOBAL (Requieren permiso sistema.usuarios) ---
        Route::middleware('permission:sistema.usuarios')->group(function () {
            Route::post('/usuarios/{usuario}/confirm-persona', [UsuarioPersonaController::class, 'confirmPersona']);
            Route::get('/usuarios/{usuario}/candidatos-persona', [UsuarioPersonaController::class, 'candidatosPersona']);
            Route::post('/usuarios/{usuario}/vincular-persona/{persona}', [UsuarioPersonaController::class, 'vincularPersona']);
            Route::post('/usuarios/{usuario}/desvincular-persona', [UsuarioPersonaController::class, 'desvincularPersona']);
            Route::post('/usuarios/{usuario}/resend-activation', [UsuarioController::class, 'resendActivation']);
            Route::post('/usuarios/{usuario}/resend-verification', [UsuarioController::class, 'resendEmailVerification']);

            Route::apiResource('usuarios', UsuarioController::class)->except(['store']);

            Route::delete('personas/{persona}/roles/{role}', [PersonaController::class, 'removeRole']);

            // Catálogo de Cargos (CRUD de maestro, el index es público vía /api/v1/cargos)
            Route::apiResource('cargos', CargoController::class)->except(['index']);

            // Gestión de Ciclos Lectivos (Panel Maestro)
            Route::apiResource('lectivos', LectivoController::class)->except(['index']);
            Route::apiResource('anios', AnioController::class);
            Route::apiResource('ambitos', AmbitoController::class);
            Route::apiResource('cierre-causas', CierreCausaController::class);
            Route::apiResource('condiciones', CondicionController::class);
            Route::apiResource('vinculo-tipos', VinculoTipoController::class);
            Route::apiResource('vinculos', VinculoController::class);
            Route::apiResource('dependencias', DependenciaController::class);
            Route::apiResource('escuela-tipos', EscuelaTipoController::class);
            Route::apiResource('niveles', NivelController::class);
            Route::apiResource('modalidades', ModalidadController::class);
            Route::apiResource('jornadas', JornadaController::class);
            Route::apiResource('turnos', TurnoController::class);
            Route::apiResource('escalafones', EscalafonController::class);
            Route::apiResource('puesto-tipos', PuestoTipoController::class);
            Route::apiResource('escuela-ubicaciones', EscuelaUbicacionController::class);
            Route::apiResource('modalidad-niveles', ModalidadNivelController::class);
            Route::apiResource('ofertas', OfertaController::class);
            Route::apiResource('documento-situacions', DocumentoSituacionController::class);
            Route::apiResource('documento-tipos', DocumentoTipoController::class);
            Route::apiResource('generos', GeneroController::class);
            Route::apiResource('sexos', SexoController::class);
            Route::apiResource('continentes', ContinenteController::class);
            Route::apiResource('naciones', NacionController::class);
            Route::apiResource('provincias', ProvinciaController::class);
            Route::apiResource('regiones', RegionController::class);
            Route::apiResource('departamentos', DepartamentoController::class);
            Route::apiResource('municipios', MunicipioController::class);
            Route::apiResource('localidades', LocalidadController::class);
            Route::apiResource('localidad-censals', LocalidadCensalController::class);
            Route::apiResource('calles', CalleController::class);

            // Catálogos Georef
            Route::apiResource('georef-fuentes', GeorefFuenteController::class);
            Route::apiResource('georef-categorias', GeorefCategoriaController::class);
            Route::apiResource('georef-funcions', GeorefFuncionController::class);
        });
    });
});
