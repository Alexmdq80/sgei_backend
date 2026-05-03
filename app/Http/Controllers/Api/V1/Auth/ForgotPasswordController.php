<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Http\Requests\Api\V1\Auth\ForgotPasswordRequest;
use App\Http\Requests\Api\V1\Auth\PasswordResetRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    /**
     * Send a reset link to the given user.
     */
    public function sendResetLinkEmail(ForgotPasswordRequest $request): JsonResponse
    {
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
    public function reset(PasswordResetRequest $request): JsonResponse
    {
        try {
            $message = $this->authService->resetPassword($request->validated());

            return response()->json(['message' => $message], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => $e->errors()['email'][0] ?? 'Error al restablecer la contraseña.',
                'code' => 422
            ], 422);
        }
    }
}
