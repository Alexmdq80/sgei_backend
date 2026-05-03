<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GeneroRequest extends FormRequest
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
        $genero = $this->route('genero');
        $id = is_object($genero) ? $genero->id : $genero;

        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('generos', 'nombre')->ignore($id)
            ],
            'orden' => ['nullable', 'integer', 'min:0', 'max:255'],
            'vigente' => ['boolean']
        ];
    }
}
