<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

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
            'nacion_id' => ['nullable', 'integer', Rule::exists('nacions', 'id')],
            'provincia_id' => ['nullable', 'integer', Rule::exists('provincias', 'id')],
            'departamento_id' => ['nullable', 'integer', Rule::exists('departamentos', 'id')],
            'localidad_id' => ['nullable', 'integer', Rule::exists('localidads', 'id')],
            'calle_id' => ['nullable', 'integer', Rule::exists('calles', 'id')],
            'calle_entre_1_id' => ['nullable', 'integer', Rule::exists('calles', 'id')],
            'calle_entre_2_id' => ['nullable', 'integer', Rule::exists('calles', 'id')],
            'numero' => ['nullable', 'string', 'max:20'],
            'piso' => ['nullable', 'string', 'max:10'],
            'departamento' => ['nullable', 'string', 'max:10'],
            'torre' => ['nullable', 'string', 'max:10'],
            'codigo_postal' => ['nullable', 'string', 'max:10'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ];
    }
    /**
     * Validación cruzada de jerarquía geográfica (contrato estricto de escritura):
     * - departamento ⇒ requiere provincia
     * - localidad    ⇒ requiere departamento
     *
     * Nota: aunque una localidad implica su departamento/provincia (el
     * DomicilioResource los deriva como fallback para registros parciales),
     * el contrato de escritura exige enviar la cascada completa desde ese
     * nivel para evitar registros parciales incoherentes. La lectura sigue
     * tolerando datos parciales (solo país / solo provincia).
     */

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            $data = $v->getData();

            $nacionExplicita = array_key_exists('nacion_id', $data);
            $hayNacion = filled($data['nacion_id'] ?? null);
            $hayProvincia = filled($data['provincia_id'] ?? null);
            $hayDepartamento = filled($data['departamento_id'] ?? null);
            $hayLocalidad = filled($data['localidad_id'] ?? null);

            // Si el país vino explícitamente en null, no puede haber geografía subnacional.
            if ($nacionExplicita && !$hayNacion && ($hayProvincia || $hayDepartamento || $hayLocalidad)) {
                $v->errors()->add(
                    'nacion_id',
                    'Debe indicarse el país cuando se informa provincia, departamento o localidad.'
                );
            }
            if ($hayDepartamento && !$hayProvincia) {
                $v->errors()->add(
                    'provincia_id',
                    'Debe indicarse la provincia cuando se informa el departamento.'
                );
            }

            if ($hayLocalidad && !$hayDepartamento) {
                $v->errors()->add(
                    'departamento_id',
                    'Debe indicarse el departamento cuando se informa la localidad.'
                );
            }
        });
    }
}
