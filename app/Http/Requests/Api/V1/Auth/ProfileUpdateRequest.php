<?php

namespace App\Http\Requests\Api\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('usuarios', 'nombre')->ignore(Auth::id())
            ],
            'documento_tipo_id' => ['nullable', 'integer', Rule::exists('documento_tipos', 'id')],
            'documento_numero' => [
                'nullable',
                'numeric',
                'digits_between:7,15',
                Rule::unique('usuarios')->where(fn ($q) => $q->where('documento_tipo_id', $this->documento_tipo_id))->ignore(Auth::id()),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('usuarios', 'email')->ignore(Auth::id())
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre de usuario es obligatorio.',
            'nombre.unique' => 'Este nombre de usuario ya está siendo utilizado.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.unique' => 'Este correo electrónico ya está siendo utilizado por otro usuario.',
            'documento_tipo_id.exists' => 'El tipo de documento seleccionado no es válido.',
            'documento_numero.numeric' => 'El número de documento debe contener solo números.',
            'documento_numero.digits_between' => 'El número de documento debe tener entre 7 y 15 dígitos.',
            'documento_numero.unique' => 'Ya existe otro usuario registrado con este tipo y número de documento.',
        ];
    }
}
