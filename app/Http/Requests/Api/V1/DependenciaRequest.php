<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DependenciaRequest extends FormRequest
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
        $dependencia = $this->route('dependencia');
        $id = is_object($dependencia) ? $dependencia->id : $dependencia;

        return [
            'nombre' => [
                'required',
                'string',
                'max:100',
                Rule::unique('dependencias', 'nombre')->ignore($id)
            ],
            'vigente' => ['nullable', 'boolean'],
        ];
    }
}
