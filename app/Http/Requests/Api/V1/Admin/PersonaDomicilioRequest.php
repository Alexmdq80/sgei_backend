<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PersonaDomicilioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Domicilio
            'localidad_id' => ['nullable', 'integer', Rule::exists('localidads', 'id')],
            'calle_id' => ['nullable', 'integer', Rule::exists('calles', 'id')],
            'calle_entre_1_id' => ['nullable', 'integer', Rule::exists('calles', 'id')],
            'calle_entre_2_id' => ['nullable', 'integer', Rule::exists('calles', 'id')],
            'numero' => ['nullable', 'string', 'max:20'],
            'piso' => ['nullable', 'string', 'max:10'],
            'departamento' => ['nullable', 'string', 'max:10'],
            'torre' => ['nullable', 'string', 'max:10'],
            'codigo_postal' => ['nullable', 'string', 'max:10'],
            'otros' => ['nullable', 'string', 'max:255'],
        ];
    }
}
