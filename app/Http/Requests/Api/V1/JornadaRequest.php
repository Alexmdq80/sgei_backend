<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class JornadaRequest extends FormRequest
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
        $jornada = $this->route('jornada');
        $id = is_object($jornada) ? $jornada->id : $jornada;

        return [
            'nombre' => [
                'required',
                'string',
                'max:100',
                Rule::unique('jornadas', 'nombre')->ignore($id)
            ],
            'orden' => ['nullable', 'integer'],
        ];
    }
}
