<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CierreCausaRequest extends FormRequest
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
        $cierreCausa = $this->route('cierre_causa');
        $id = is_object($cierreCausa) ? $cierreCausa->id : $cierreCausa;

        return [
            'nombre' => [
                'required',
                'string',
                'max:150',
                Rule::unique('cierre_causas', 'nombre')->ignore($id)
            ],
            'vigente' => ['nullable', 'boolean'],
        ];
    }
}
