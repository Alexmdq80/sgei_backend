<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $usuario = $this->route('usuario');
        $id = is_object($usuario) ? $usuario->id : $usuario;

        return [
            'nombre' => ['required', 'string', 'max:255'],
            'documento_tipo_id' => ['nullable', 'integer', Rule::exists('documento_tipos', 'id')],
            'documento_numero' => ['nullable', 'string', 'max:20'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('usuarios', 'email')->ignore($id)
            ],
            'password' => ['nullable', 'string', Password::defaults()],
            'updated_at' => ['nullable', 'date'],
        ];
    }
}
