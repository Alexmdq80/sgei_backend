<?php

namespace App\Http\Resources;

use App\Models\EscuelaPersona;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin EscuelaPersona
 */
class EscuelaPersonaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->resource instanceof EscuelaPersona) {
            $loads = [];
            if ($this->relationLoaded('persona')) {
                $loads[] = 'persona.documentoTipo';
            }
            if ($this->relationLoaded('escuela')) {
                $loads[] = 'escuela.localidad';
            }
            if (!empty($loads)) {
                $this->resource->loadMissing($loads);
            }
        }

        return [
            'id' => $this->id,
            'escuela_id' => $this->escuela_id,
            'persona_id' => $this->persona_id,
            'role_id' => $this->role_id,
            'verified_at' => $this->verified_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'persona' => new PersonaResource($this->whenLoaded('persona')),
            'escuela' => new EscuelaResource($this->whenLoaded('escuela')),
            'role' => new RoleResource($this->whenLoaded('role')),
        ];
    }
}