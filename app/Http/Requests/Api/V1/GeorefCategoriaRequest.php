<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GeorefCategoriaRequest extends FormRequest
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
        $georefCategoria = $this->route('georef_categoria');
        $id = is_object($georefCategoria) ? $georefCategoria->id : $georefCategoria;

        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('georef_categorias', 'nombre')->ignore($id)
            ],
            'orden' => ['nullable', 'integer'],
            'vigente' => ['boolean']
        ];
    }
}
