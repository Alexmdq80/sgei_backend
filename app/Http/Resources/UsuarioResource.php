<?php

namespace App\Http\Resources;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Usuario
 */
class UsuarioResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->resource instanceof Usuario) {
            $loads = [];
            if ($this->relationLoaded('persona') && $this->persona) {
                if ($this->persona->relationLoaded('escuelasPersonas')) {
                    $loads[] = 'persona.escuelasPersonas.escuela';
                    $loads[] = 'persona.escuelasPersonas.role';
                }
            }
            if (!empty($loads)) {
                $this->resource->loadMissing($loads);
            }
        }

        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'documento_tipo_id' => $this->documento_tipo_id,
            'documento_numero' => $this->documento_numero,
            'es_administrador' => $this->es_administrador,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'has_password' => $this->has_password,
            'estado' => $this->estado,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'avatar_url' => $this->avatar_url,
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
            'permissions' => $this->when(
                $this->relationLoaded('permissions') || $this->relationLoaded('roles'),
                fn() => $this->getAllPermissions()->pluck('name')
            ),
            'documento_tipo' => $this->whenLoaded('documentoTipo'),
            'persona' => new PersonaResource($this->whenLoaded('persona')),
            'escuelas_personas' => $this->when(
                $this->relationLoaded('persona') && $this->persona?->relationLoaded('escuelasPersonas'),
                fn() => EscuelaPersonaResource::collection($this->persona->escuelasPersonas)
            )
        ];
    }
}