<?php

namespace App\Services;

use App\Models\Usuario;
use App\Models\AuthenticationAudit;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class AuthService
{
    /**
     * Authenticate a user and create a token.
     *
     * @param array<string, mixed> $credentials
     * @param Request $request
     * @return array<string, mixed>
     * @throws ValidationException
     */
    public function login(array $credentials, Request $request): array
    {
        $identifier = $credentials['email'] ?? "Doc: {$credentials['documento_tipo_id']}-{$credentials['documento_numero']}";

        if (!Auth::guard('web')->attempt($credentials)) {
            $this->auditLogin($identifier, 'login_failed', $request);
            
            throw ValidationException::withMessages([
                'login' => ['Las credenciales proporcionadas son incorrectas.'],
            ])->status(401);
        }

        /** @var Usuario $usuario */
        $usuario = Auth::user();
        
        // Check if email is verified
        if (!$usuario->hasVerifiedEmail()) {
            Auth::guard('web')->logout();
            $this->auditLogin($identifier, 'login_failed_unverified', $request, $usuario);

            throw ValidationException::withMessages([
                'login' => ['Debes verificar tu correo electrónico antes de iniciar sesión.'],
            ])->status(401);
        }

        \Illuminate\Support\Facades\Log::info('Login attempt successful', [
            'attempted_identifier' => $identifier,
            'actual_user_email' => $usuario->email,
            'actual_user_id' => $usuario->id
        ]);

        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        $this->auditLogin($identifier, 'login_success', $request, $usuario);

        $token = $usuario->createToken('auth_token')->plainTextToken;

        return [
            'user' => $usuario,
            'token' => $token
        ];
    }

    /**
     * Log out and invalidate the session.
     */
    public function logout(Usuario $usuario, Request $request): void
    {
        // Revocar el token actual de Sanctum si existe
        $usuario->currentAccessToken()?->delete();

        Auth::guard('web')->logout();
        
        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }
    }

    /**
     * Record an authentication attempt in the audit table.
     *
     * @param string $identifier
     * @param string $event
     * @param Request $request
     * @param Usuario|null $usuario
     * @return void
     */
    protected function auditLogin(string $identifier, string $event, Request $request, ?Usuario $usuario = null): void
    {
        AuthenticationAudit::create([
            'auditable_type' => $usuario ? get_class($usuario) : null,
            'auditable_id' => $usuario ? $usuario->id : null,
            'event' => $event,
            'attempted_email' => str_contains($identifier, '@') ? $identifier : null,
            'url' => $request->fullUrl(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'details' => !str_contains($identifier, '@') ? ['identifier' => $identifier] : null,
            'audit_driver' => 'authentication'
        ]);
    }
}
