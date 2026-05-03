<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MunicipioRequest extends FormRequest
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
        $municipio = $this->route('municipio');
        $id = is_object($municipio) ? $municipio->id : $municipio;

        return [
            'provincia_id' => [
                'required',
                Rule::exists('provincias', 'id')
            ],
            'nombre' => [
                'required',
                'string',
                'max:255'
            ],
            'id_georef' => [
                'nullable',
                Rule::unique('municipios', 'id_georef')->ignore($id)
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
            'id_georef.unique' => 'El ID Georef ya está asignado a otro municipio.',
        ];
    }
}
