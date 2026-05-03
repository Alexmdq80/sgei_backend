<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PropuestaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'escuela_id' => ['required', Rule::exists('escuelas', 'id')],
            'anio_plan_id' => ['required', Rule::exists('anio_plan', 'id')],
            'turno_inicio_id' => ['nullable', Rule::exists('turnos', 'id')],
            'turno_fin_id' => ['nullable', Rule::exists('turnos', 'id')],
            'jornada_id' => ['nullable', Rule::exists('jornadas', 'id')],
            'lectivo_id' => ['required', Rule::exists('lectivos', 'id')],
        ];
    }
}
