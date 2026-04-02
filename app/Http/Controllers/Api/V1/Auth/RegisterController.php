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
            'nombre' => 'required|string|max:255|unique:usuarios,nombre',
            'email' => 'required|string|email|max:255|unique:usuarios',
            'documento_tipo_id' => 'required|integer|exists:documento_tipos,id',
            'documento_numero' => [
                'required',
                'numeric',
                'digits_between:7,15',
                \Illuminate\Validation\Rule::unique('usuarios')->where(function ($query) use ($request) {
                    return $query->where('documento_tipo_id', $request->documento_tipo_id);
                }),
            ],
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'nombre.required' => 'El nombre de usuario es obligatorio.',
            'nombre.unique' => 'Este nombre de usuario ya está siendo utilizado.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'documento_tipo_id.required' => 'El tipo de documento es obligatorio.',
            'documento_numero.required' => 'El número de documento es obligatorio.',
            'documento_numero.numeric' => 'El número de documento debe contener solo números.',
            'documento_numero.digits_between' => 'El número de documento debe tener entre 7 y 15 dígitos.',
            'documento_numero.unique' => 'Ya existe un usuario registrado con este tipo y número de documento.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'password.min' => 'La contraseña debe tener al menos 10 caracteres.',
            'password.letters' => 'La contraseña debe incluir al menos una letra.',
            'password.mixed' => 'La contraseña debe incluir letras mayúsculas y minúsculas.',
            'password.numbers' => 'La contraseña debe incluir al menos un número.',
            'password.symbols' => 'La contraseña debe incluir al menos un símbolo.',
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
