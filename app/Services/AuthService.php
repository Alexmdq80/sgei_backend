<?php

namespace App\Services;

use App\Models\Usuario;
use App\Models\AuthenticationAudit;
use App\Models\RefreshToken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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

        // Siempre invalidar la sesión actual antes de un nuevo login para evitar colisiones entre usuarios
        Auth::guard('web')->logout();
        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        if (!Auth::guard('web')->attempt($credentials)) {
            $this->auditLogin($identifier, 'login_failed', $request);
            
            \Illuminate\Support\Facades\Log::warning('Login fallido:', ['identifier' => $identifier]);

            throw ValidationException::withMessages([
                'login' => ['Las credenciales proporcionadas son incorrectas.'],
            ])->status(401);
        }

        // Recuperar el usuario explícitamente del guard web para evitar estados cacheados
        /** @var Usuario $usuario */
        $usuario = Auth::guard('web')->user();

        if (!$usuario) {
            throw new \Exception('Error crítico: No se pudo recuperar el usuario tras autenticación exitosa.');
        }
        
        \Illuminate\Support\Facades\Log::info('Login exitoso:', [
            'identifier' => $identifier,
            'user_id' => $usuario->id,
            'user_email' => $usuario->email
        ]);

        \Illuminate\Support\Facades\Log::info('Login attempt successful', [
            'attempted_identifier' => $identifier,
            'actual_user_email' => $usuario->email,
            'actual_user_id' => $usuario->id
        ]);

        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        $this->auditLogin($identifier, 'login_success', $request, $usuario);

        $token = $usuario->createToken('auth-token')->plainTextToken;
        $refreshToken = $this->createRefreshToken($usuario);

        return [
            'user' => $usuario,
            'token' => $token,
            'refresh_token' => $refreshToken->token
        ];
    }

    /**
     * Create a new refresh token for the user.
     */
    protected function createRefreshToken(Usuario $user): RefreshToken
    {
        return RefreshToken::create([
            'usuario_id' => $user->id,
            'token' => Str::random(64),
            'expires_at' => now()->addDays(7), // Long-lived (7 days)
            'device_id' => request()->userAgent() // Simple device tracking
        ]);
    }

    /**
     * Refresh the access token using a valid refresh token.
     */
    public function refreshToken(string $token): array
    {
        $refreshToken = RefreshToken::where('token', $token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$refreshToken) {
            throw ValidationException::withMessages([
                'refresh_token' => ['El token de refresco es inválido o ha expirado.'],
            ])->status(401);
        }

        $usuario = $refreshToken->usuario;
        
        // Rotate the Refresh Token: Generate a new one and delete the old one
        $newRefreshToken = $this->createRefreshToken($usuario);
        $refreshToken->delete();

        // Generate new access token
        $newAccessToken = $usuario->createToken('auth-token')->plainTextToken;

        return [
            'token' => $newAccessToken,
            'refresh_token' => $newRefreshToken->token
        ];
    }

    /**
     * Send a password reset link to the given user.
     */
    public function forgotPassword(string $email): string
    {
        $status = Password::broker()->sendResetLink(['email' => $email]);

        if ($status !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return __($status);
    }

    /**
     * Reset the given user's password.
     */
    public function resetPassword(array $credentials): string
    {
        $status = Password::broker()->reset(
            $credentials,
            function (Usuario $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(\Illuminate\Support\Str::random(60));

                $user->save();
                
                // Revocar todos los tokens actuales para forzar re-login
                $user->tokens()->delete();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return __($status);
    }

    /**
     * Log out and invalidate the session.
     */
    public function logout(Usuario $usuario, Request $request): void
    {
        // Revoke current access token only if it's not a TransientToken (used by Sanctum for cookies)
        $token = $usuario->currentAccessToken();
        if ($token && method_exists($token, 'delete')) {
            $token->delete();
        }

        // Revoke refresh token if provided
        if ($request->has('refresh_token')) {
            RefreshToken::where('token', $request->refresh_token)
                ->where('usuario_id', $usuario->id)
                ->delete();
        }

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
