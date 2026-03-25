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
     * @param string $email
     * @param string $password
     * @param Request $request
     * @return array<string, mixed>
     * @throws ValidationException
     */
    public function login(string $email, string $password, Request $request): array
    {
        if (!Auth::guard('web')->attempt(['email' => $email, 'password' => $password])) {
            $this->auditLogin($email, 'login_failed', $request);
            
            throw ValidationException::withMessages([
                'email' => ['Credenciales inválidas.'],
            ]);
        }

        $usuario = Auth::user();
        $request->session()->regenerate(); // Regenerar sesión para prevenir ataques

        $this->auditLogin($email, 'login_success', $request, $usuario);

        return [
            'user' => $usuario
        ];
    }

    /**
     * Log out and invalidate the session.
     */
    public function logout(Usuario $usuario, Request $request): void
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    /**
     * Record an authentication attempt in the audit table.
     *
     * @param string $email
     * @param string $event
     * @param Request $request
     * @param Usuario|null $usuario
     * @return void
     */
    protected function auditLogin(string $email, string $event, Request $request, ?Usuario $usuario = null): void
    {
        AuthenticationAudit::create([
            'auditable_type' => $usuario ? get_class($usuario) : null,
            'auditable_id' => $usuario ? $usuario->id : null,
            'event' => $event,
            'attempted_email' => $email,
            'url' => $request->fullUrl(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'audit_driver' => 'authentication'
        ]);
    }
}
