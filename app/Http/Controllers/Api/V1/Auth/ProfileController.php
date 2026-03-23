<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

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
        return response()->json(Auth::user());
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:usuarios,email,' . Auth::id(),
        ]);

        $user = $this->userService->updateProfile(Auth::user(), $validatedData);

        return response()->json([
            'message' => 'Perfil actualizado con éxito.',
            'user' => $user
        ]);
    }

    /**
     * Update the user's avatar.
     */
    public function updateAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $avatarUrl = $this->userService->updateAvatar(Auth::user(), $request->file('avatar'));

        return response()->json([
            'message' => 'Avatar actualizado con éxito.',
            'avatar_url' => $avatarUrl
        ]);
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $this->userService->updatePassword(Auth::user(), $request->current_password, $request->password);

            return response()->json([
                'message' => 'Contraseña actualizada con éxito.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'La contraseña actual es incorrecta.',
                'code' => 422
            ], 422);
        }
    }
}
