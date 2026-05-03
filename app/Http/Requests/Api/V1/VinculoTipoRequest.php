<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VinculoTipoRequest extends FormRequest
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
        $vinculoTipo = $this->route('vinculo_tipo');
        $id = is_object($vinculoTipo) ? $vinculoTipo->id : $vinculoTipo;

        return [
            'nombre' => [
                'required',
                'string',
                'max:150',
                Rule::unique('vinculo_tipos', 'nombre')->ignore($id)
            ],
            'orden' => ['nullable', 'integer'],
            'vigente' => ['boolean'],
        ];
    }
}
