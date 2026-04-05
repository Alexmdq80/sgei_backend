<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // 1. Esto es lo ÚNICO que necesitas para que Sanctum funcione con React.
        // Internamente ya añade StartSession, VerifyCsrfToken, etc., a las rutas de la API.
        $middleware->statefulApi();

        // 2. NO hagas prepend manual de StartSession o VerifyCsrfToken aquí, 
        // ya que statefulApi() se encarga de inyectarlos en el orden correcto.

        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
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
        $exceptions->render(function (\Illuminate\Http\Exceptions\ThrottleRequestsException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'error' => 'Has realizado demasiadas peticiones. Por favor, espera un momento antes de volver a intentarlo.',
                    'code' => 429
                ], 429);
            }
        });
    })->create();
