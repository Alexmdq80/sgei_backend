<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProvinciaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('nombre')) {
            $this->merge(['nombre' => mb_strtoupper($this->nombre, 'UTF-8')]);
        }
    }

    public function rules(): array
    {
        $provincia = $this->route('provincia');
        $id = is_object($provincia) ? $provincia->id : $provincia;

        return [
            'nacion_id' => ['required', Rule::exists('nacions', 'id')],
            'nombre' => ['required', 'string', 'max:255', Rule::unique('provincias', 'nombre')->ignore($id)],
            'id_georef' => ['nullable', Rule::unique('provincias', 'id_georef')->ignore($id)],
            'iso_id' => ['nullable', 'string', 'max:10']
        ];
    }

    public function messages(): array
    {
        return [
            'id_georef.unique' => 'El ID Georef ingresado ya está asignado a otra provincia.',
            'nombre.unique' => 'Ya existe una provincia con este nombre.'
        ];
    }
}
