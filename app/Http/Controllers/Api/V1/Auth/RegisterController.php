<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\UsuarioResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Handle a registration request for the application.
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:usuarios',
            'documento_tipo_id' => 'required|integer|exists:documento_tipos,id',
            'documento_numero' => 'required|string|max:20',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Error de validación',
                'errors' => $validator->errors(),
                'code' => 422
            ], 422);
        }

        $data = $validator->validated();
        // Forzar que no sea administrador en registro público
        $data['es_administrador'] = false;
        $data['estado'] = 'email_pendiente';

        try {
            $user = $this->userService->create($data);

            return response()->json([
                'message' => 'Usuario registrado con éxito. Por favor, verifica tu correo electrónico.',
                'user' => new UsuarioResource($user)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'No se pudo completar el registro. Intente más tarde.',
                'code' => 500
            ], 500);
        }
    }
}
