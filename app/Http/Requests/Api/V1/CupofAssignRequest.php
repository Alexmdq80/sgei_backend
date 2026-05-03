<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CupofAssignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'persona_id' => ['required', Rule::exists('personas', 'id')],
            'situacion_revista' => ['required', Rule::in(['titular', 'provisional', 'suplente'])],
            'fecha_inicio' => ['required', 'date'],
            'resolucion' => ['nullable', 'string']
        ];
    }
}
