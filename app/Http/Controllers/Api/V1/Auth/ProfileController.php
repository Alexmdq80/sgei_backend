<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Http\Requests\Api\V1\Auth\ProfileUpdateRequest;
use App\Http\Requests\Api\V1\Auth\AvatarUpdateRequest;
use App\Http\Requests\Api\V1\Auth\PasswordUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UsuarioResource;

class ProfileController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Get the authenticated user's profile.
     */
    public function me(): JsonResponse
    {
        $usuario = Auth::user();
        $usuario->refresh();

        return response()->json([
            'user' => new UsuarioResource($usuario->load([
                'persona',
                'documentoTipo',
                'roles',
                'persona.escuelasPersonas.escuela',
                'persona.escuelasPersonas.role'
            ]))
        ]);
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(ProfileUpdateRequest $request): JsonResponse
    {
        $dto = \App\DTOs\User\UpdateUserProfileDTO::fromRequest($request);
        $user = $this->userService->updateProfile(Auth::user(), $dto);

        return response()->json([
            'message' => 'Perfil actualizado con éxito.',
            'user' => $user->load(['persona', 'documentoTipo', 'persona.escuelasPersonas.escuela', 'persona.escuelasPersonas.role'])
        ]);
    }

    /**
     * Update the user's avatar.
     */
    public function updateAvatar(AvatarUpdateRequest $request): JsonResponse
    {
        try {
            $avatarUrl = $this->userService->updateAvatar(Auth::user(), $request->file('avatar'));

            return response()->json([
                'message' => 'Avatar actualizado con éxito.',
                'avatar_url' => $avatarUrl
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error al subir avatar: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al subir el avatar.',
                'code' => 500
            ], 500);
        }
    }

    /**
     * Delete the user's avatar.
     */
    public function deleteAvatar(): JsonResponse
    {
        try {
            $this->userService->deleteAvatar(Auth::user());
            return response()->json([
                'message' => 'Avatar eliminado con éxito.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al eliminar el avatar.',
                'code' => 500
            ], 500);
        }
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(PasswordUpdateRequest $request): JsonResponse
    {
        try {
            $this->userService->updatePassword(Auth::user(), $request->current_password, $request->password);

            return response()->json([
                'message' => 'Contraseña actualizada con éxito.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'code' => 400
            ], 400);
        }
    }
}
