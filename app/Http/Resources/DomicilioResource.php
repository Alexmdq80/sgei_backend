<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Domicilio;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Domicilio
 */
class DomicilioResource extends JsonResource
{
    /** Recibe un modelo Domicilio (o null si la persona aún no tiene). */
    public function toArray(Request $request): ?array
    {
        $domicilio = $this->resource;
        if (!($domicilio instanceof Domicilio)) {
            return null;
        }

        return [
            'nacion_id' => $domicilio->nacion_id,
            'nacion_nombre' => $domicilio->nacion?->nombre,
            'provincia_id' => $domicilio->provincia_id ?? $domicilio->localidad?->departamento?->provincia_id,
            'provincia_nombre' => $domicilio->provincia?->nombre ?? $domicilio->localidad?->departamento?->provincia?->nombre,
            'departamento_id' => $domicilio->departamento_id ?? $domicilio->localidad?->departamento_id,
            'departamento_nombre' => $domicilio->departamento?->nombre ?? $domicilio->localidad?->departamento?->nombre,
            'localidad_id' => $domicilio->localidad_id,
            'localidad_nombre' => $domicilio->localidad?->nombre,
            'calle_id' => $domicilio->calle_id,
            'calle_nombre' => $domicilio->calle_nombre ?? $domicilio->calle?->nombre,
            'calle_entre_1_id' => $domicilio->calle_entre_1_id,
            'calle_entre_1_nombre' => $domicilio->calle_entre_1_nombre ?? $domicilio->entreCalle1?->nombre,
            'calle_entre_2_id' => $domicilio->calle_entre_2_id,
            'calle_entre_2_nombre' => $domicilio->calle_entre_2_nombre ?? $domicilio->entreCalle2?->nombre,
            'numero' => $domicilio->numero,
            'piso' => $domicilio->piso,
            'unidad' => $domicilio->unidad,
            'torre' => $domicilio->torre,
            'codigo_postal' => $domicilio->codigo_postal,
            'observaciones' => $domicilio->observaciones,
        ];
    }
}
