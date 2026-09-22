<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\DTOs\User\CreateUserDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\RegisterRequest;
use App\Http\Resources\UsuarioResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

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
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Forzar que no sea administrador en registro público
        $data['es_administrador'] = false;
        $data['estado'] = 'email_pendiente';

        try {
            $dto = CreateUserDTO::fromArray($data);
            $user = $this->userService->create($dto);

            return response()->json([
                'message' => 'Usuario registrado con éxito. Por favor, verifica tu correo electrónico.',
                'user' => new UsuarioResource($user),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'No se pudo completar el registro. Intente más tarde.',
                'code' => 500,
            ], 500);
        }
    }
}
