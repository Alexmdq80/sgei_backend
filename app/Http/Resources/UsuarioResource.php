<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsuarioResource extends JsonResource
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
            'documento_tipo_id' => $this->documento_tipo_id,
            'documento_numero' => $this->documento_numero,
            'es_administrador' => $this->es_administrador,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'estado' => $this->estado,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'avatar_url' => $this->avatar_url,
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
            'permissions' => $this->getAllPermissions()->pluck('name'),
            'documento_tipo' => $this->whenLoaded('documentoTipo'),
            'persona' => $this->whenLoaded('persona'),
            'escuela_usuarios' => EscuelaUsuarioResource::collection($this->whenLoaded('escuelaUsuarios')),
            'provincia_usuario' => $this->provinciaUsuario?->loadMissing('provincia'),
            'region_usuario' => $this->regionUsuario?->loadMissing('region'),
            'distrito_usuario' => $this->distritoUsuario?->loadMissing(['distrito', 'distrito.departamento']),
        ];
    }
}
