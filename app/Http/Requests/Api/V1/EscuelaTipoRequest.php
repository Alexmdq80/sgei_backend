<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EscuelaTipoRequest extends FormRequest
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
        $escuelaTipo = $this->route('escuela_tipo');
        $id = is_object($escuelaTipo) ? $escuelaTipo->id : $escuelaTipo;

        return [
            'nombre' => [
                'required',
                'string',
                'max:100',
                Rule::unique('escuela_tipos', 'nombre')->ignore($id)
            ],
            'vigente' => ['nullable', 'boolean'],
        ];
    }
}
