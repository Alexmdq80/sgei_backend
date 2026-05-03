<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DocumentoSituacionRequest extends FormRequest
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
        $documentoSituacion = $this->route('documento_situacion');
        $id = is_object($documentoSituacion) ? $documentoSituacion->id : $documentoSituacion;

        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('documento_situacions', 'nombre')->ignore($id)
            ],
            'vigente' => ['boolean']
        ];
    }
}
