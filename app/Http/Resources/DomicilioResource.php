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
        if (! ($domicilio instanceof Domicilio)) {
            return null;
        }

        return [
            'localidad_id' => $domicilio->localidad_id,
            'localidad_nombre' => $domicilio->localidad?->nombre,
            'calle_id' => $domicilio->calle_id,
            'calle_nombre' => $domicilio->calle?->nombre,
            'calle_entre_1_id' => $domicilio->calle_entre_1_id,
            'calle_entre_1_nombre' => $domicilio->entreCalle1?->nombre,
            'calle_entre_2_id' => $domicilio->calle_entre_2_id,
            'calle_entre_2_nombre' => $domicilio->entreCalle2?->nombre,
            'numero' => $domicilio->numero,
            'piso' => $domicilio->piso,
            'departamento' => $domicilio->departamento,
            'torre' => $domicilio->torre,
            'codigo_postal' => $domicilio->codigo_postal,
            'otros' => $domicilio->otros,
        ];
    }
}
