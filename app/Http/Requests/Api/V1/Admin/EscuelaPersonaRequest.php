<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EscuelaPersonaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'persona_id' => ['required', 'integer', Rule::exists('personas', 'id')],
            'escuela_id' => ['required', 'integer', Rule::exists('escuelas', 'id')],
            'role_id' => ['required', 'integer', Rule::exists('roles', 'id')]
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            return [
                'role_id' => ['required', 'integer', Rule::exists('roles', 'id')]
            ];
        }

        return $rules;
    }
}