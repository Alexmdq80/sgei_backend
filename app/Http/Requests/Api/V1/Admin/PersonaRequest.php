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
    }

    public function rules(): array
    {
        $persona = $this->route('persona');
        $id = is_object($persona) ? $persona->id : $persona;
        
        // El ID del contacto para ignorar en el unique de email
        $contactoId = is_object($persona) ? $persona->contacto?->id : null;

        return [
            'apellido' => ['required', 'string', 'max:255'],
            'nombre' => ['required', 'string', 'max:255'],
            'documento_tipo_id' => ['required', 'integer', Rule::exists('documento_tipos', 'id')],
            'documento_numero' => [
                'required', 
                'string', 
                'max:20', 
                Rule::unique('personas', 'documento_numero')->ignore($id)
            ],
            'email' => [
                'nullable', 
                'email', 
                'max:255', 
                Rule::unique('contactos', 'email')->ignore($contactoId)
            ],
            'cuil' => ['nullable', 'string', 'max:15'],
            'fecha_nacimiento' => ['nullable', 'date'],
            'genero_id' => ['nullable', 'integer', Rule::exists('generos', 'id')],
            'nacionalidad_id' => ['nullable', 'integer', Rule::exists('nacions', 'id')],
        ];
    }

    public function messages(): array
    {
        return [
            'documento_numero.unique' => 'Este número de documento ya se encuentra registrado en el padrón de personas.',
            'email.unique' => 'Este correo electrónico ya está asignado a otra persona en el padrón.'
        ];
    }
}
