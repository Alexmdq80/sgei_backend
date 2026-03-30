<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Verify the user's email address.
     */
    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:usuarios,email',
            'token' => 'required|string',
        ]);

        $user = Usuario::where('email', $request->email)->first();

        // Si el usuario ya está verificado, devolvemos éxito directamente
        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'El correo electrónico ya ha sido verificado.',
                'code' => 200
            ]);
        }

        // Si no está verificado, validamos el token
        if ($user->verification_token !== $request->token) {
            return response()->json([
                'message' => 'Token de verificación inválido.',
                'code' => 400
            ], 400);
        }

        // Validar expiración (24 horas)
        if ($user->isVerificationTokenExpired()) {
            return response()->json([
                'message' => 'El enlace de verificación ha expirado. Por favor, solicita uno nuevo.',
                'code' => 400
            ], 400);
        }

        $user->markEmailAsVerified();

        return response()->json([
            'message' => 'Correo electrónico verificado con éxito.',
            'code' => 200
        ]);
    }

    /**
     * Resend the email verification notification.
     */
    public function resend(Request $request): JsonResponse
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'El correo electrónico ya ha sido verificado.',
                'code' => 200
            ]);
        }

        $this->userService->resendVerification($user);

        return response()->json([
            'message' => 'Se ha enviado un nuevo enlace de verificación a tu correo electrónico.',
            'code' => 200
        ]);
    }
}
