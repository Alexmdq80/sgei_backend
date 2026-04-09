<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class PlanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Controlado por Policies
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'nombre_completo' => 'required|string|max:500',
            'duracion_anios' => 'required|integer|min:1|max:10',
            'resolucion' => 'nullable|string|max:255',
            'orientacion' => 'nullable|string|max:255',
            'plan_ciclo_id' => 'required|exists:plan_ciclos,id',
        ];
    }
}
