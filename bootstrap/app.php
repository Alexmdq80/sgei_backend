<?php

use App\Http\Middleware\BlockPanelGeneralAccess;
use App\Http\Middleware\EnsureEmailIsVerifiedWithBypass;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    // 🔒 1. Habilitar la autenticación de canales para Sanctum / React
    ->withBroadcasting(
        __DIR__.'/../routes/channels.php',
        ['prefix' => 'api', 'middleware' => ['web', 'auth:sanctum']],
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // 1. Esto es lo ÚNICO que necesitas para que Sanctum funcione con React.
        // Internamente ya añade StartSession, VerifyCsrfToken, etc., a las rutas de la API.
        $middleware->statefulApi();

        // 2. NO hagas prepend manual de StartSession o VerifyCsrfToken aquí,
        // ya que statefulApi() se encarga de inyectarlos en el orden correcto.

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'verified' => EnsureEmailIsVerifiedWithBypass::class,
            'block_panel_general' => BlockPanelGeneralAccess::class,
        ]);

        // 3. TrustProxies es correcto si usas Apache como Proxy.
        $middleware->trustProxies(at: '*');

        // 4. Exceptuar rutas de autenticación pública del CSRF
        $middleware->validateCsrfTokens(except: [
            'api/v1/auth/forgot-password',
            'api/v1/auth/reset-password',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ThrottleRequestsException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'error' => 'Has realizado demasiadas peticiones. Por favor, espera un momento antes de volver a intentarlo.',
                    'code' => 429,
                ], 429);
            }
        });
    })->create();
