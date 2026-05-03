<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ModalidadNivelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $modalidadNivel = $this->route('modalidad_nivel');
        $id = is_object($modalidadNivel) ? $modalidadNivel->id : $modalidadNivel;

        $rules = [
            'nivel_id' => ['required', Rule::exists('nivels', 'id')],
            'modalidad_id' => ['required', Rule::exists('modalidads', 'id')],
            'escuela_tipo_id' => ['nullable', Rule::exists('escuela_tipos', 'id')],
        ];

        // Validar unicidad de la combinación en store
        if (!$id) {
            $rules['nivel_id'][] = Rule::unique('modalidad_nivels')->where(function ($query) {
                return $query->where('modalidad_id', $this->modalidad_id)
                    ->where('escuela_tipo_id', $this->escuela_tipo_id);
            });
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'nivel_id.unique' => 'Esta combinación de Nivel, Modalidad y Tipo de Escuela ya existe.',
        ];
    }
}
