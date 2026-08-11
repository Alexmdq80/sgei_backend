<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Services\UserService;
use App\Http\Requests\Api\V1\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\Api\V1\Auth\CompleteSetupRequest;
use App\Http\Requests\Api\V1\Auth\ResendActivationRequest;
use App\Notifications\AccountInvitationNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Notifications\VerifyEmailNotification;

class VerificationController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Resend the account activation invitation (Public).
     */
    public function resendActivation(ResendActivationRequest $request): JsonResponse
    {
        $user = Usuario::where('email', $request->email)->first();

        if ($user->hasVerifiedEmail() || $user->estado === 'activo') {
            return response()->json([
                'message' => 'Esta cuenta ya está activa.',
                'code' => 200
            ]);
        }

        DB::transaction(function () use ($user) {
            $user->verification_token = Str::random(60);
            $user->verification_token_created_at = now();
            $user->save();

            if ($user->estado === 'esperando_activacion') {
                $user->notify(new AccountInvitationNotification($user->verification_token));
            } else {
                $user->notify(new VerifyEmailNotification($user->verification_token));
            }
        });

        return response()->json([
            'message' => 'Se ha enviado un nuevo enlace de activación a tu correo electrónico.',
            'code' => 200
        ]);
    }

    /**
     * Finalize the account setup by setting a password and verifying the email.
     */
    public function completeSetup(CompleteSetupRequest $request): JsonResponse
    {
        $user = Usuario::where('email', $request->email)->first();

        // Validar token
        if ($user->verification_token !== $request->token) {
            return response()->json([
                'error' => 'Token de activación inválido.',
                'code' => 400
            ], 400);
        }

        // Validar expiración (24 horas)
        if ($user->isVerificationTokenExpired()) {
            return response()->json([
                'error' => 'El enlace de activación ha expirado.',
                'code' => 400
            ], 400);
        }

        DB::transaction(function () use ($user, $request) {
            $user->password = Hash::make($request->password);
            $user->password_set = true;
            $user->verification_token = null;
            $user->verification_token_created_at = null;
            $user->estado = $user->email_verified_at ? 'activo' : 'email_pendiente';
            $user->save();
        });

        return response()->json([
            'message' => 'Cuenta activada con éxito. Ya puedes iniciar sesión.',
            'code' => 200
        ]);
    }

    /**
     * Verify the user's email address.
     */
    public function verify(EmailVerificationRequest $request): JsonResponse
    {
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
