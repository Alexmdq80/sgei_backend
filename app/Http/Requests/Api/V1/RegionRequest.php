<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $region = $this->route('regione'); // Laravel defaults the parameter to 'regione' for resource 'regiones'
        $id = is_object($region) ? $region->id : $region;

        return [
            'provincia_id' => ['required', Rule::exists('provincias', 'id')],
            'numero' => [
                'required', 
                'string', 
                'max:50', 
                Rule::unique('regions')->where(function ($query) {
                    return $query->where('provincia_id', $this->provincia_id);
                })->ignore($id)
            ],
            'vigente' => ['boolean']
        ];
    }

    public function messages(): array
    {
        return [
            'numero.unique' => 'Ya existe una región con este número para la provincia seleccionada.'
        ];
    }
}
