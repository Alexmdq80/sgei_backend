<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PersonaDomicilioContactoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $persona = $this->route('persona');
        $contactoId = $persona?->contacto?->id;

        return [
            // Contacto
            'telefono_codigo_area' => ['nullable', 'string', 'max:10', 'regex:/^[0-9]+$/'],
            'telefono' => ['nullable', 'string', 'max:20', 'regex:/^[0-9]+$/'],
            'celular_codigo_area' => ['nullable', 'string', 'max:10', 'regex:/^[0-9]+$/'],
            'celular' => ['nullable', 'string', 'max:20', 'regex:/^[0-9]+$/'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('contactos', 'email')->ignore($contactoId)],

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

    public function messages(): array
    {
        return [
            'telefono_codigo_area.regex' => 'El código de área del teléfono debe contener solo números.',
            'telefono.regex' => 'El teléfono debe contener solo números.',
            'celular_codigo_area.regex' => 'El código de área del celular debe contener solo números.',
            'celular.regex' => 'El celular debe contener solo números.',
            'email.email' => 'El email debe tener un formato válido.',
            'email.unique' => 'Este email ya está asignado a otra persona.',
        ];
    }
}
