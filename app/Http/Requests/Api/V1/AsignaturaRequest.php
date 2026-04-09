<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class AsignaturaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Controlado por Gate en el Controlador
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
            'nombre_completo' => 'nullable|string|max:500',
            'anio_plan_id' => 'required|exists:anio_plan,id',
            'horas_semanales' => 'required|integer|min:0|max:40',
            'codigo' => 'nullable|string|max:50',
            'orden' => 'integer|min:0'
        ];
    }
}
