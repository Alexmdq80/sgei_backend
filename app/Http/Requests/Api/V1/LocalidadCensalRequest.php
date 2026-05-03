<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LocalidadCensalRequest extends FormRequest
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
        $localidadCensal = $this->route('localidad_censal');
        $id = is_object($localidadCensal) ? $localidadCensal->id : $localidadCensal;

        return [
            'nombre' => ['required', 'string', 'max:255'],
            'id_georef' => ['nullable', 'string', 'max:255', Rule::unique('localidad_censals', 'id_georef')->ignore($id)],
            'georef_fuente_id' => ['nullable', Rule::exists('georef_fuentes', 'id')],
            'georef_categoria_id' => ['nullable', Rule::exists('georef_categorias', 'id')],
            'georef_funcion_id' => ['nullable', Rule::exists('georef_funcions', 'id')],
            'centroide_lat' => ['nullable', 'numeric'],
            'centroide_lon' => ['nullable', 'numeric']
        ];
    }
}
