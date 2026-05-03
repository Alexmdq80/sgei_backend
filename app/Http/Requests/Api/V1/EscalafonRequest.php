<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EscalafonRequest extends FormRequest
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
        $escalafon = $this->route('escalafon');
        $id = is_object($escalafon) ? $escalafon->id : $escalafon;

        return [
            'nombre' => [
                'required',
                'string',
                'max:150',
                Rule::unique('escalafones', 'nombre')->ignore($id)
            ],
            'orden' => ['nullable', 'integer'],
            'vigente' => ['boolean'],
        ];
    }
}
