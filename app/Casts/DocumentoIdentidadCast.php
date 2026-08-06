<?php

declare(strict_types=1);

namespace App\Casts;

use App\ValueObjects\DocumentoIdentidad;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class DocumentoIdentidadCast implements CastsAttributes
{
    /**
     * Transforma el valor de la BD al Value Object.
     * Se ejecuta al leer: $persona->documento_numero
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?DocumentoIdentidad
    {
        if ($value === null || $value === '') {
            return null;
        }

        $tipoId = $attributes['documento_tipo_id'] ?? $model->documento_tipo_id;

        if ($tipoId === null) {
            throw new InvalidArgumentException(
                'No se puede construir DocumentoIdentidad sin un documento_tipo_id.'
            );
        }

        return new DocumentoIdentidad((int) $tipoId, (string) $value);
    }

    /**
     * Transforma el Value Object a los valores de la BD.
     * Se ejecuta al guardar: $persona->documento_numero = $dni;
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): array
    {
        if ($value === null) {
            return [
                'documento_numero' => null,
                'documento_tipo_id' => null,
            ];
        }

        if ($value instanceof DocumentoIdentidad) {
            return [
                'documento_numero' => $value->numero(),
                'documento_tipo_id' => $value->tipoId(),
            ];
        }

        if (is_string($value)) {
            $tipoId = $model->documento_tipo_id ?? $attributes['documento_tipo_id'] ?? null;

            if ($tipoId === null) {
                throw new InvalidArgumentException(
                    'No se puede establecer el número de documento sin un documento_tipo_id.'
                );
            }

            return [
                'documento_numero' => (new DocumentoIdentidad((int) $tipoId, $value))->numero(),
            ];
        }

        throw new InvalidArgumentException(
            'El valor debe ser una instancia de ' . DocumentoIdentidad::class . ' o un string.'
        );
    }
}
