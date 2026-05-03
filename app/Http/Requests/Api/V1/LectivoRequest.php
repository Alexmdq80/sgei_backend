<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class LectivoRequest extends FormRequest
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
            'nombre' => ['required', 'string', 'max:255'],
            'anio' => ['required', 'integer', 'min:2000', 'max:2100'],
            'orden' => ['nullable', 'integer'],
            'vigente' => ['boolean'],
            'cerrado' => ['boolean'],
        ];
    }
}
