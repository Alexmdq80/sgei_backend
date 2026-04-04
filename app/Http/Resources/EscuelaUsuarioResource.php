<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EscuelaUsuarioResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'escuela_id' => $this->escuela_id,
            'usuario_id' => $this->usuario_id,
            'verified_at' => $this->verified_at,
            'role_id' => $this->role_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'usuario' => new UsuarioResource($this->whenLoaded('usuario')),
            'escuela' => new EscuelaResource($this->whenLoaded('escuela')),
            'role' => new RoleResource($this->whenLoaded('role')),
        ];
    }
}
