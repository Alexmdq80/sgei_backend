<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PersonaContactoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('observaciones') && is_string($value = $this->input('observaciones'))) {
            $sanitized = str_replace(["\r\n", "\r"], "\n", $value);
            $sanitized = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $sanitized) ?? $sanitized;
            $this->merge(['observaciones' => trim($sanitized)]);
        }
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
            'observaciones' => ['nullable', 'string', 'max:1000'],
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
