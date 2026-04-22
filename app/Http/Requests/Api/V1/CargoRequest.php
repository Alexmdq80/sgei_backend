<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class CargoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Handled by middleware
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('nombre')) {
            $this->merge([
                'nombre' => mb_strtoupper($this->nombre, 'UTF-8'),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'nombre' => 'required|string|max:255|unique:cargos,nombre',
            'requiere_cursos' => 'boolean',
            'activo' => 'boolean',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $cargo = $this->route('cargo');
            $id = is_object($cargo) ? $cargo->id : $cargo;
            $rules['nombre'] = 'required|string|max:255|unique:cargos,nombre,' . $id;
        }

        return $rules;
    }
}
