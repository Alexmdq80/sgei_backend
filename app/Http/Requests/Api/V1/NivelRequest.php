<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NivelRequest extends FormRequest
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
        $nivel = $this->route('nivel');
        $id = is_object($nivel) ? $nivel->id : $nivel;

        return [
            'nombre' => [
                'required',
                'string',
                'max:100',
                Rule::unique('nivels', 'nombre')->ignore($id)
            ],
            'vigente' => ['nullable', 'boolean'],
        ];
    }
}
