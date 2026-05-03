<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EscuelaRequest extends FormRequest
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
        $escuela = $this->route('escuela');
        $id = is_object($escuela) ? $escuela->id : $escuela;

        return [
            'nombre' => ['required', 'string', 'max:255'],
            'numero' => ['required', 'string', 'max:50'],
            'cue_anexo' => ['required', 'string', 'max:50', Rule::unique('escuelas', 'cue_anexo')->ignore($id)],
            'localidad_id' => ['required', Rule::exists('georef_localidads', 'id')],
            'ambito_id' => ['nullable', Rule::exists('ambitos', 'id')],
            'dependencia_id' => ['nullable', Rule::exists('dependencias', 'id')],
            'sector_id' => ['nullable', Rule::exists('escuela_tipos', 'id')],
            'email' => ['nullable', 'email', 'max:255'],
        ];
    }
}
