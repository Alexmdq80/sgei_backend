<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContinenteRequest extends FormRequest
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
        $continente = $this->route('continente');
        $id = is_object($continente) ? $continente->id : $continente;

        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('continentes', 'nombre')->ignore($id)
            ],
            'vigente' => ['boolean']
        ];
    }
}
