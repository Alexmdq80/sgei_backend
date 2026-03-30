<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password;

class ForgotPasswordController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    /**
     * Send a reset link to the given user.
     */
    public function sendResetLinkEmail(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        try {
            $message = $this->authService->forgotPassword($request->email);

            return response()->json(['message' => $message], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => $e->errors()['email'][0] ?? 'Error al enviar el enlace.',
                'code' => 422
            ], 422);
        }
    }

    /**
     * Reset the user's password.
     */
    public function reset(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        try {
            $message = $this->authService->resetPassword($request->all());

            return response()->json(['message' => $message], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => $e->errors()['email'][0] ?? 'Error al restablecer la contraseña.',
                'code' => 422
            ], 422);
        }
    }
}
