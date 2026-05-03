<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Requests\Api\V1\Auth\RefreshTokenRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected AuthService $authService
    ) {}

    /**
     * Authenticate a user and return a token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only(['password']);

        if ($request->has('email')) {
            $credentials['email'] = $request->string('email');
        } else {
            $credentials['documento_tipo_id'] = $request->integer('documento_tipo_id');
            $credentials['documento_numero'] = $request->string('documento_numero');
        }

        try {
            $data = $this->authService->login($credentials, $request);

            return response()->json([
                'user' => new \App\Http\Resources\UsuarioResource($data['user']->load(['persona', 'documentoTipo', 'roles', 'escuelaUsuarios.escuela', 'escuelaUsuarios.role'])),
                'token' => $data['token'],
                'refresh_token' => $data['refresh_token']
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'error' => $e->validator->errors()->first(),
                'code' => $e->status
            ], $e->status);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Ocurrió un error inesperado durante la autenticación.',
                'code' => 500
            ], 500);
        }
    }

    /**
     * Refresh the access token.
     */
    public function refresh(RefreshTokenRequest $request): JsonResponse
    {
        try {
            $data = $this->authService->refreshToken($request->refresh_token);

            return response()->json($data, 200);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => $e->errors()['refresh_token'][0] ?? 'Token de refresco inválido.',
                'code' => 401
            ], 401);
        }
    }

    /**
     * Log out the user and revoke the token.
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
