<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class AnioRequest extends FormRequest
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
        return [
            'nombre' => ['required', 'string', 'max:100'],
            'nombre_completo' => ['nullable', 'string', 'max:255'],
            'anio_absoluto' => ['nullable', 'integer'],
            'anio_relativo' => ['nullable', 'integer'],
            'vigente' => ['nullable', 'boolean'],
        ];
    }
}
