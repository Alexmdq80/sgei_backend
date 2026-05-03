<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EscuelaUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'usuario_id' => ['required', 'uuid', Rule::exists('usuarios', 'id')],
            'escuela_id' => ['required', 'integer', Rule::exists('escuelas', 'id')],
            'role_id' => ['required', 'integer', Rule::exists('roles', 'id')]
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            // En update solo permitimos cambiar el rol usualmente
            return [
                'role_id' => ['required', 'integer', Rule::exists('roles', 'id')]
            ];
        }

        return $rules;
    }
}
