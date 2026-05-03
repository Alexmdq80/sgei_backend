<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CondicionRequest extends FormRequest
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
        $condicion = $this->route('condicion');
        $id = is_object($condicion) ? $condicion->id : $condicion;

        return [
            'nombre' => [
                'required',
                'string',
                'max:100',
                Rule::unique('condicions', 'nombre')->ignore($id)
            ],
            'vigente' => ['nullable', 'boolean'],
        ];
    }
}
