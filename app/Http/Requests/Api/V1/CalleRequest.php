<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CalleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Autenticación gestionada por middleware
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
        $calle = $this->route('calle');
        $id = is_object($calle) ? $calle->id : $calle;

        return [
            'nombre' => [
                'required',
                'string',
                'max:255'
            ],
            'localidad_censal_id' => [
                'required',
                Rule::exists('localidad_censals', 'id')
            ],
            'id_georef' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('calles', 'id_georef')->ignore($id)
            ],
            'altura_inicio_derecha' => [
                'nullable',
                'integer'
            ],
            'altura_inicio_izquierda' => [
                'nullable',
                'integer'
            ],
            'altura_fin_derecha' => [
                'nullable',
                'integer'
            ],
            'altura_fin_izquierda' => [
                'nullable',
                'integer'
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'id_georef.unique' => 'El ID Georef ya está asignado a otra calle.',
        ];
    }
}
