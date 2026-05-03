<?php

namespace App\Http\Requests\Api\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255', Rule::unique('usuarios', 'nombre')],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('usuarios', 'email')],
            'documento_tipo_id' => ['required', 'integer', Rule::exists('documento_tipos', 'id')],
            'documento_numero' => [
                'required',
                'numeric',
                'digits_between:7,15',
                Rule::unique('usuarios')->where(fn ($q) => $q->where('documento_tipo_id', $this->documento_tipo_id)),
            ],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }

    public function messages(): array
    {
        return [
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
        ];
    }
}
