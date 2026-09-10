<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Contacto;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Contacto
 */
class ContactoResource extends JsonResource
{
    /** Recibe un modelo Contacto (o null si la persona aún no tiene). */
    public function toArray(Request $request): ?array
    {
        $contacto = $this->resource;
        if (! ($contacto instanceof Contacto)) {
            return null;
        }

        return [
            'telefono_codigo_area' => $contacto->telefono_codigo_area,
            'telefono' => $contacto->telefono,
            'celular_codigo_area' => $contacto->celular_codigo_area,
            'celular' => $contacto->celular,
            'email' => $contacto->email,
            'observaciones' => $contacto->observaciones,
        ];
    }
}
