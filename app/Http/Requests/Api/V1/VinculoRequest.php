<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VinculoRequest extends FormRequest
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
        $vinculo = $this->route('vinculo');
        $id = is_object($vinculo) ? $vinculo->id : $vinculo;

        return [
            'vinculo_tipo_id' => ['required', Rule::exists('vinculo_tipos', 'id')],
            'nombre' => [
                'required',
                'string',
                'max:150',
                Rule::unique('vinculos', 'nombre')->ignore($id)
            ],
            'orden' => ['nullable', 'integer'],
            'vigente' => ['boolean'],
        ];
    }
}
