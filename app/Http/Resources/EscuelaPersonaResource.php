<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EscuelaPersonaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'escuela_id' => $this->escuela_id,
            'persona_id' => $this->persona_id,
            'verified_at' => $this->verified_at,
            'role_id' => $this->role_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'persona' => new PersonaResource($this->whenLoaded('persona')),
            'escuela' => new EscuelaResource($this->whenLoaded('escuela')),
            'role' => new RoleResource($this->whenLoaded('role')),
        ];
    }
}
