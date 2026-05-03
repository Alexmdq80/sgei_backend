<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GeorefFuenteRequest extends FormRequest
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
        $georefFuente = $this->route('georef_fuente');
        $id = is_object($georefFuente) ? $georefFuente->id : $georefFuente;

        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('georef_fuentes', 'nombre')->ignore($id)
            ],
            'orden' => ['nullable', 'integer'],
            'vigente' => ['boolean']
        ];
    }
}
