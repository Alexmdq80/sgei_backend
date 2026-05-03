<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class AgenteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'persona_id' => ['required', 'exists:personas,id', 'unique:agentes,persona_id'],
            'legajo' => ['nullable', 'string', 'unique:agentes,legajo'],
            'fecha_ingreso_sistema' => ['nullable', 'date'],
        ];
    }
}
