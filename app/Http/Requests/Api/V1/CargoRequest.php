<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        $cargo = $this->route('cargo');
        $id = is_object($cargo) ? $cargo->id : $cargo;

        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('cargos', 'nombre')->ignore($id)
            ],
            'tipo' => ['required', 'string', Rule::in(['horas', 'modulos', 'cargo'])],
            'escalafon_id' => ['nullable', 'integer', 'exists:escalafones,id'],
            'requiere_cursos' => ['boolean'],
            'activo' => ['boolean'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del cargo es obligatorio.',
            'nombre.unique' => 'Ya existe un cargo registrado con este nombre.',
            'tipo.required' => 'El tipo de designación es obligatorio.',
            'tipo.in' => 'El tipo de designación seleccionado no es válido.',
            'escalafon_id.exists' => 'El escalafón seleccionado no es válido.',
        ];
    }
}
