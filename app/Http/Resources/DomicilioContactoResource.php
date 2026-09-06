<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DomicilioContactoResource extends JsonResource
{
    /**
     * Recibe un array con claves 'domicilio' y 'contacto'.
     * Expone IDs (para poblar el formulario) + nombres (para el combobox).
     */
    public function toArray($request): array
    {
        $domicilio = $this->resource['domicilio'] ?? null;
        $contacto = $this->resource['contacto'] ?? null;

        return [
            'domicilio' => $domicilio ? [
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
            ] : null,
            'contacto' => $contacto ? [
                'telefono_codigo_area' => $contacto->telefono_codigo_area,
                'telefono' => $contacto->telefono,
                'celular_codigo_area' => $contacto->celular_codigo_area,
                'celular' => $contacto->celular,
                'email' => $contacto->email,
            ] : null,
        ];
    }
}
