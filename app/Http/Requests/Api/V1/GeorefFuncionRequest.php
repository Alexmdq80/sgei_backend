<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GeorefFuncionRequest extends FormRequest
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
        $georefFuncion = $this->route('georef_funcion');
        $id = is_object($georefFuncion) ? $georefFuncion->id : $georefFuncion;

        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('georef_funcions', 'nombre')->ignore($id)
            ],
            'orden' => ['nullable', 'integer'],
            'vigente' => ['boolean']
        ];
    }
}
