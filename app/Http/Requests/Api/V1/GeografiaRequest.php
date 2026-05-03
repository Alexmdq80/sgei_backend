<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GeografiaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [];

        if ($this->routeIs('geografia.departamentos')) {
            $rules['provincia_id'] = ['required', 'integer', Rule::exists('provincias', 'id')];
        }

        if ($this->routeIs('geografia.localidades')) {
            $rules['departamento_id'] = ['required', 'integer', Rule::exists('departamentos', 'id')];
        }

        return $rules;
    }
}
