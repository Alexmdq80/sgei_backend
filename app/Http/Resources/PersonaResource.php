<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PersonaResource extends JsonResource
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
            'apellido' => $this->apellido,
            'nombre_completo' => "{$this->apellido}, {$this->nombre}",
            'documento_tipo_id' => $this->documento_tipo_id,
            'documento_tipo' => $this->documentoTipo ? [
                'id' => $this->documentoTipo->id,
                'nombre' => $this->documentoTipo->nombre,
            ] : null,
            'documento_numero' => $this->documento_numero,
            'CUIL_prefijo' => $this->CUIL_prefijo,
            'CUIL_sufijo' => $this->CUIL_sufijo,
            'cuil' => $this->CUIL_prefijo . "-" . $this->documento_numero . "-" . $this->CUIL_sufijo,
            'nacimiento_fecha' => $this->nacimiento_fecha?->format('Y-m-d'),
            'genero' => $this->genero ? [
                'id' => $this->genero->id,
                'nombre' => $this->genero->nombre,
            ] : null,
            'usuario_id' => $this->usuario_id,
            'usuario_email' => $this->usuario?->email,
            'nacionalidad' => $this->nacionalidad?->nombre,
            'nacimiento_pais' => $this->nacimientoPais?->nombre,
            'nacimiento_provincia' => $this->nacimientoProvincia?->nombre,
            'nacimiento_localidad' => $this->nacimientoLocalidad?->nombre,
            'domicilio' => $this->domicilio ? [
                'calle' => $this->domicilio->calle?->nombre,
                'numero' => $this->domicilio->numero,
                'piso' => $this->domicilio->piso,
                'depto' => $this->domicilio->depto,
                'barrio' => $this->domicilio->barrio,
            ] : null,
            'contacto' => $this->contacto ? [
                'telefono_fijo' => $this->contacto->telefono_fijo,
                'telefono_movil' => $this->contacto->telefono_movil,
                'email' => $this->contacto->email,
            ] : null,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
