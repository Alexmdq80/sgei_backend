<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SexoRequest extends FormRequest
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
        if ($this->has('letra')) {
            $this->merge(['letra' => mb_strtoupper($this->letra, 'UTF-8')]);
        }
    }

    public function rules(): array
    {
        $sexo = $this->route('sexo');
        $id = is_object($sexo) ? $sexo->id : $sexo;

        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sexos', 'nombre')->ignore($id)
            ],
            'letra' => ['required', 'string', 'max:1'],
            'orden' => ['nullable', 'integer', 'min:0', 'max:255'],
            'vigente' => ['boolean']
        ];
    }
}
