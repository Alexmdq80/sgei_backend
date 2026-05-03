<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LocalidadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
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
        $localidad = $this->route('localidad');
        $id = is_object($localidad) ? $localidad->id : $localidad;

        return [
            'departamento_id' => [
                'required',
                Rule::exists('departamentos', 'id')
            ],
            'localidad_censal_id' => [
                'nullable',
                Rule::exists('localidad_censals', 'id')
            ],
            'nombre' => [
                'required',
                'string',
                'max:255'
            ],
            'id_georef' => [
                'nullable',
                'integer',
                Rule::unique('localidads', 'id_georef')->ignore($id)
            ],
        ];
    }
}
