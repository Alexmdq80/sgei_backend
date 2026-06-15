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
            'usuario' => new UsuarioResource($this->whenLoaded('usuario')),
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
            'relaciones' => $this->getRelacionesInstitucionales(),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Aggregates all institutional roles for the person.
     */
    private function getRelacionesInstitucionales(): array
    {
        $relaciones = [];

        // 1. Relación Laboral (CUPOF)
        if ($this->relationLoaded('movimientosCupofActivos')) {
            foreach ($this->movimientosCupofActivos as $movimiento) {
                $escalafon = mb_strtolower($movimiento->cupof->escalafon->nombre ?? '', 'UTF-8');
                $cargo = mb_strtoupper($movimiento->cupof->nombre_cargo ?? '', 'UTF-8');
                
                if (str_contains($escalafon, 'docente')) {
                    $relaciones[] = "DOCENTE ({$cargo})";
                } elseif (str_contains($escalafon, 'auxiliar')) {
                    $relaciones[] = "AUXILIAR ({$cargo})";
                } elseif (str_contains($escalafon, 'administrativo')) {
                    $relaciones[] = "ADMINISTRATIVO ({$cargo})";
                } else {
                    $relaciones[] = "PERSONAL ({$cargo})";
                }
            }
        }

        // 2. Relación Académica (Estudiante)
        if ($this->relationLoaded('inscripcion') && $this->inscripcion) {
            $relaciones[] = "ESTUDIANTE";
        }

        // 3. Relación Familiar/Vínculo (Adulto responsable/autorizado)
        if ($this->relationLoaded('vinculosComoAdulto')) {
            foreach ($this->vinculosComoAdulto as $v) {
                // El pivot es PersonaVinculoPersona, que tiene la relación 'vinculo'
                $nombreVinculo = mb_strtoupper($v->pivot->vinculo->nombre ?? 'VÍNCULO', 'UTF-8');
                $relaciones[] = $nombreVinculo;
            }
        }

        return array_values(array_unique($relaciones));
    }
}
