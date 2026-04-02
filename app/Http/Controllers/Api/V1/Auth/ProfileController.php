<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
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
        
        return response()->json([
            'user' => new UsuarioResource($usuario->load(['persona', 'documentoTipo', 'escuelaUsuarios.escuela', 'escuelaUsuarios.rolEscolar']))
        ]);
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255|unique:usuarios,nombre,' . Auth::id(),
            'documento_tipo_id' => 'nullable|integer|exists:documento_tipos,id',
            'documento_numero' => [
                'nullable',
                'numeric',
                'digits_between:7,15',
                \Illuminate\Validation\Rule::unique('usuarios')
                    ->where(function ($query) use ($request) {
                        return $query->where('documento_tipo_id', $request->documento_tipo_id);
                    })
                    ->ignore(Auth::id()),
            ],
            'email' => 'required|email|max:255|unique:usuarios,email,' . Auth::id(),
        ], [
            'nombre.required' => 'El nombre de usuario es obligatorio.',
            'nombre.unique' => 'Este nombre de usuario ya está siendo utilizado.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.unique' => 'Este correo electrónico ya está siendo utilizado por otro usuario.',
            'documento_tipo_id.exists' => 'El tipo de documento seleccionado no es válido.',
            'documento_numero.numeric' => 'El número de documento debe contener solo números.',
            'documento_numero.digits_between' => 'El número de documento debe tener entre 7 y 15 dígitos.',
            'documento_numero.unique' => 'Ya existe otro usuario registrado con este tipo y número de documento.',
        ]);

        $user = $this->userService->updateProfile(Auth::user(), $validatedData);

        return response()->json([
            'message' => 'Perfil actualizado con éxito.',
            'user' => $user->load(['persona', 'documentoTipo', 'escuelaUsuarios.escuela', 'escuelaUsuarios.rolEscolar'])
        ]);
    }

    /**
     * Update the user's avatar.
     */
    public function updateAvatar(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $avatarUrl = $this->userService->updateAvatar(Auth::user(), $request->file('avatar'));

            return response()->json([
                'message' => 'Avatar actualizado con éxito.',
                'avatar_url' => $avatarUrl
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Illuminate\Support\Facades\Log::error('Error de validación en avatar: ' . json_encode($e->errors()));
            return response()->json([
                'error' => 'Los datos proporcionados no son válidos.',
                'errors' => $e->errors(),
                'code' => 422
            ], 422);
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
    public function updatePassword(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'current_password' => 'required|string',
                'password' => [
                    'required',
                    'confirmed',
                    Password::defaults()
                ]
            ], [
                'current_password.required' => 'La contraseña actual es obligatoria.',
                'password.required' => 'La nueva contraseña es obligatoria.',
                'password.confirmed' => 'La confirmación de la nueva contraseña no coincide.',
                'password.min' => 'La contraseña debe tener al menos 10 caracteres.',
                'password.letters' => 'La contraseña debe incluir al menos una letra.',
                'password.mixed' => 'La contraseña debe incluir letras mayúsculas y minúsculas.',
                'password.numbers' => 'La contraseña debe incluir al menos un número.',
                'password.symbols' => 'La contraseña debe incluir al menos un símbolo.',
            ]);

            $this->userService->updatePassword(Auth::user(), $request->current_password, $request->password);

            return response()->json([
                'message' => 'Contraseña actualizada con éxito.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Error de validación.',
                'errors' => $e->errors(),
                'code' => 422
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'code' => 400
            ], 400);
        }
    }
}
