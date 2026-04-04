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
            'email' => 'required_without_all:documento_tipo_id,documento_numero|email',
            'documento_tipo_id' => 'required_without:email|integer|exists:documento_tipos,id',
            'documento_numero' => 'required_without:email|numeric|digits_between:7,15',
            'password' => 'required|string',
        ], [
            'documento_numero.numeric' => 'El número de documento debe contener solo números.',
            'documento_numero.digits_between' => 'El número de documento debe tener entre 7 y 15 dígitos.',
        ]);

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
                'user' => new \App\Http\Resources\UsuarioResource($data['user']->load(['persona', 'documentoTipo', 'escuelaUsuarios.escuela', 'escuelaUsuarios.role'])),
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
    public function refresh(Request $request): JsonResponse
    {
        $request->validate(['refresh_token' => 'required|string']);

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
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->validate([
            'refresh_token' => 'sometimes|string'
        ]);

        $this->authService->logout($request->user(), $request);

        return response()->json([
            'message' => 'Sesión cerrada correctamente.',
            'code' => 200
        ], 200);
    }
}
