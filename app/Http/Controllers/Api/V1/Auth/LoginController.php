<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param AuthService $authService
     */
    public function __construct(
        protected AuthService $authService
    ) {}

    /**
     * Authenticate a user and return a token.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        try {
            $data = $this->authService->login(
                $request->string('email'),
                $request->string('password'),
                $request
            );

            return response()->json([
                'user' => $data['user']
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'code' => 401
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Ocurrió un error inesperado durante la autenticación.',
                'code' => 500
            ], 500);
        }
    }

    /**
     * Log out the user and revoke the token.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user(), $request);

        return response()->json([
            'message' => 'Sesión cerrada correctamente.',
            'code' => 200
        ], 200);
    }
}
