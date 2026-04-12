<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class EnsureEmailIsVerifiedWithBypass
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // --- BYPASS PARA SUPERUSUARIOS ---
        // El superusuario puede acceder a todo sin haber verificado email (Bypass de emergencia)
        if ($user->es_administrador || $user->hasRole('superuser')) {
            return $next($request);
        }

        // --- RESTRICCIÓN PARA EL RESTO ---
        if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            return response()->json([
                'error' => 'Tu dirección de correo electrónico no ha sido verificada.',
                'code' => 403,
                'require_verification' => true
            ], 403);
        }

        return $next($request);
    }
}
