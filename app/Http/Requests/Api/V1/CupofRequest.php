<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CupofRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'codigo_cupof' => ['required', 'string', Rule::unique('cupofs', 'codigo_cupof')],
            'escuela_id' => ['required', Rule::exists('escuelas', 'id')],
            'asignatura_id' => ['nullable', Rule::exists('asignaturas', 'id')],
            'escalafon_id' => ['required', Rule::exists('escalafones', 'id')],
            'puesto_tipo_id' => ['required', Rule::exists('puesto_tipos', 'id')],
            'nombre_cargo' => ['nullable', 'string', 'max:255'],
            'cantidad' => ['integer', 'min:1']
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $cupof = $this->route('cupof');
            $id = is_object($cupof) ? $cupof->id : $cupof;
            $rules['codigo_cupof'] = ['required', 'string', Rule::unique('cupofs', 'codigo_cupof')->ignore($id)];
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'codigo_cupof.required' => 'El código CUPOF es obligatorio.',
            'codigo_cupof.unique' => 'Ya existe un puesto registrado con este código CUPOF.',
            'escuela_id.required' => 'Debe seleccionar una institución.',
            'escuela_id.exists' => 'La institución seleccionada no es válida.',
            'escalafon_id.required' => 'El escalafón es obligatorio.',
            'puesto_tipo_id.required' => 'El tipo de puesto es obligatorio.',
            'cantidad.integer' => 'La cantidad debe ser un número entero.',
            'cantidad.min' => 'La cantidad debe ser al menos 1.',
        ];
    }
}
