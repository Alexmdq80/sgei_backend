<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PersonaRequest extends FormRequest
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
        if ($this->has('apellido')) {
            $this->merge(['apellido' => mb_strtoupper($this->apellido, 'UTF-8')]);
        }
        if ($this->has('nombre_alternativo')) {
            $this->merge(['nombre_alternativo' => mb_strtoupper($this->nombre_alternativo, 'UTF-8')]);
        }
        $this->sanitizeObservaciones();
    }

    public function rules(): array
    {
        $persona = $this->route('persona');
        $id = is_object($persona) ? $persona->id : $persona;

        // El ID del contacto para ignorar en el unique de email
        $contactoId = is_object($persona) ? $persona->contacto?->id : null;

        // Si se envía vive_si = 0 (fallecida) => el email queda PROHIBIDO
        $esFallecida = in_array($this->input('vive_si'), [0, '0', false], true);

        $rules = [
            'apellido' => ['required', 'string', 'max:255', 'regex:/^[\pL\s\'-]+$/u'],
            'nombre' => ['required', 'string', 'max:255', 'regex:/^[\pL\s\'-]+$/u'],
            'nombre_alternativo' => ['nullable', 'string', 'max:255', 'regex:/^[\pL\s\'-]+$/u'],
            'sexo_id' => ['nullable', 'integer', Rule::exists('sexos', 'id')],
            'genero_id' => ['nullable', 'integer', Rule::exists('generos', 'id')],
            'nacionalidad_nacion_id' => ['nullable', 'integer', Rule::exists('nacions', 'id')],
            'nacimiento_fecha' => ['nullable', 'date'],
            'documento_situacion_id' => ['nullable', 'integer', Rule::exists('documento_situacions', 'id')],
            'documento_tipo_id' => ['required', 'integer', Rule::exists('documento_tipos', 'id')],
            'documento_numero' => [
                'required_unless:documento_tipo_id,7',
                'nullable',
                'string',
                'max:20',
                Rule::unique('personas', 'documento_numero')->ignore($id),
            ],
            'tramite' => ['nullable', 'string', 'max:50'],
            'CUIL_prefijo' => ['nullable', 'string', 'max:2'],
            'CUIL_sufijo' => ['nullable', 'string', 'max:1'],
            'cuil' => ['nullable', 'string', 'max:15'],
            'nacion_id' => ['nullable', 'integer', Rule::exists('nacions', 'id')],
            'provincia_id' => ['nullable', 'integer', Rule::exists('provincias', 'id')],
            'departamento_id' => ['nullable', 'integer', Rule::exists('departamentos', 'id')],
            'localidad_id' => ['nullable', 'integer', Rule::exists('localidads', 'id')],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
            'email' => $esFallecida
                ? ['prohibited']
                : ['nullable', 'email', 'max:255', Rule::unique('contactos', 'email')->ignore($contactoId)],
            'vive_si' => ['nullable', 'boolean'],
            'confirmed' => ['sometimes', 'boolean'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ];

        return $rules;
    }

    /**
     * Higiene mínima del campo observaciones (texto libre, opcional):
     * - Normaliza saltos de línea (CRLF/CR -> LF) para consistencia multiplataforma.
     * - Descarta caracteres de control (excepto \n y \t) que no aportan valor.
     * - Aplica trim() para eliminar espacios y saltos sobrantes al inicio/fin.
     *
     * Si llega null (borrado intencional del campo) se respeta intacto.
     */
    protected function sanitizeObservaciones(): void
    {
        if ($this->has('observaciones') && is_string($value = $this->input('observaciones'))) {
            $sanitized = str_replace(["\r\n", "\r"], "\n", $value);
            $sanitized = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $sanitized) ?? $sanitized;
            $this->merge(['observaciones' => trim($sanitized)]);
        }
    }

    public function messages(): array
    {
        return [
            'documento_tipo_id.required' => 'El tipo de documento es obligatorio.',
            'documento_numero.required_unless' => 'El número de documento es obligatorio salvo que la persona sea INDOCUMENTADA (tipo 7).',
            'documento_numero.unique' => 'Este número de documento ya se encuentra registrado en el padrón de personas.',
            'email.unique' => 'Este correo electrónico ya está asignado a otra persona en el padrón.',
            'apellido.regex' => 'El campo :attribute solo puede contener letras, espacios, guiones y apóstrofes.',
            'nombre.regex' => 'El campo :attribute solo puede contener letras, espacios, guiones y apóstrofes.',
            'nombre_alternativo.regex' => 'El campo :attribute solo puede contener letras, espacios, guiones y apóstrofes.',
        ];
    }
}
