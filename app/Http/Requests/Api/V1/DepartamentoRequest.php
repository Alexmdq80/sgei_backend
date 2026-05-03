<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DepartamentoRequest extends FormRequest
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
        $departamento = $this->route('departamento');
        $id = is_object($departamento) ? $departamento->id : $departamento;

        return [
            'provincia_id' => ['required', Rule::exists('provincias', 'id')],
            'nombre' => ['required', 'string', 'max:255'],
            'id_georef' => ['nullable', Rule::unique('departamentos', 'id_georef')->ignore($id)]
        ];
    }
}
