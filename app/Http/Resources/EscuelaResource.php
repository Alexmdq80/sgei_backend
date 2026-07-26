<?php

namespace App\Http\Resources;

use App\Models\Escuela;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Escuela
 */
class EscuelaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'numero' => $this->numero,
            'cue_anexo' => $this->cue_anexo,
            'clave_provincial' => $this->clave_provincial,
            'localidad_id' => $this->localidad_id,
            'sector_id' => $this->sector_id,
            'localidad' => $this->whenLoaded('localidad'),
            'sector' => $this->whenLoaded('sector'),
            'ambito' => $this->whenLoaded('ambito'),
            'dependencia' => $this->whenLoaded('dependencia'),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}