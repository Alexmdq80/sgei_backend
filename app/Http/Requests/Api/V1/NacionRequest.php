<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NacionRequest extends FormRequest
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
        if ($this->has('nacionalidad')) {
            $this->merge(['nacionalidad' => mb_strtoupper($this->nacionalidad, 'UTF-8')]);
        }
    }

    public function rules(): array
    {
        $nacion = $this->route('nacion');
        $id = is_object($nacion) ? $nacion->id : $nacion;

        return [
            'id_georef' => ['nullable'],
            'continente_id' => ['required', Rule::exists('continentes', 'id')],
            'nombre' => ['required', 'string', 'max:255', Rule::unique('nacions', 'nombre')->ignore($id)],
            'nacionalidad' => ['required', 'string', 'max:255']
        ];
    }
}
