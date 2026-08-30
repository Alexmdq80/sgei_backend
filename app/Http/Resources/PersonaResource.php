<?php

namespace App\Http\Resources;

use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Persona
 */
class PersonaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->resource instanceof Persona) {
            $loads = [];
            if ($this->relationLoaded('movimientosCupofActivos')) {
                $loads[] = 'movimientosCupofActivos.cupof.escalafon';
            }
            if ($this->relationLoaded('vinculosComoAdulto')) {
                $loads[] = 'vinculosComoAdulto.pivot.vinculo';
            }
            if ($this->relationLoaded('domicilio')) {
                $loads[] = 'domicilio.calle';
            }
            if (!empty($loads)) {
                $this->resource->loadMissing($loads);
            }
        }

        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'nombre_completo' => "{$this->apellido}, {$this->nombre}",
            'foto_url' => $this->foto_url,
            'documento_tipo_id' => $this->documento_tipo_id,
            'documento_tipo' => $this->whenLoaded('documentoTipo', fn() => [
                'id' => $this->documentoTipo->id,
                'nombre' => $this->documentoTipo->nombre,
            ]),
            'documento_situacion_id' => $this->documento_situacion_id,
            'documento_situacion' => $this->whenLoaded('documentoSituacion', fn() => [
                'id' => $this->documentoSituacion->id,
                'nombre' => $this->documentoSituacion->nombre,
            ]),
            'nombre_alternativo' => $this->nombre_alternativo,
            'sexo_id' => $this->sexo_id,
            'sexo' => $this->whenLoaded('sexo', fn() => [
                'id' => $this->sexo->id,
                'nombre' => $this->sexo->nombre,
                'letra' => $this->sexo->letra,
            ]),
            'genero_id' => $this->genero_id,
            // 'genero' ya está definido más abajo como objeto {id, nombre} ✓
            'nacionalidad_nacion_id' => $this->nacionalidad_nacion_id,
            'nacion_id' => $this->nacion_id,
            'provincia_id' => $this->provincia_id,
            'departamento_id' => $this->departamento_id,
            'nacimiento_departamento' => $this->whenLoaded('nacimientoDepartamento', fn() => $this->nacimientoDepartamento?->nombre),
            'localidad_id' => $this->localidad_id,
            'tramite' => $this->tramite,
            'documento_numero' => $this->documentoNumeroRaw(),
            'CUIL_prefijo' => $this->CUIL_prefijo,
            'CUIL_sufijo' => $this->CUIL_sufijo,
            'cuil' => ($this->CUIL_prefijo && $this->CUIL_sufijo)
                ? "{$this->CUIL_prefijo}-{$this->documento_numero}-{$this->CUIL_sufijo}"
                : null,
            'nacimiento_fecha' => $this->nacimiento_fecha?->format('Y-m-d'),
            'genero' => $this->whenLoaded('genero', fn() => [
                'id' => $this->genero->id,
                'nombre' => $this->genero->nombre,
            ]),
            'usuario_id' => $this->usuario_id,
            'usuario_email' => $this->whenLoaded('usuario', fn() => $this->usuario?->email),
            'usuario' => new UsuarioResource($this->whenLoaded('usuario')),
            'nacionalidad' => $this->whenLoaded('nacionalidad', fn() => $this->nacionalidad?->nombre),
            'nacimiento_pais' => $this->whenLoaded('nacimientoPais', fn() => $this->nacimientoPais?->nombre),
            'nacimiento_provincia' => $this->whenLoaded('nacimientoProvincia', fn() => $this->nacimientoProvincia?->nombre),
            'nacimiento_localidad' => $this->whenLoaded('nacimientoLocalidad', fn() => $this->nacimientoLocalidad?->nombre),
            'domicilio' => $this->whenLoaded('domicilio', fn() => [
                'calle' => $this->domicilio->calle?->nombre,
                'numero' => $this->domicilio->numero,
                'piso' => $this->domicilio->piso,
                'depto' => $this->domicilio->depto,
                'barrio' => $this->domicilio->barrio,
            ]),
            'contacto' => $this->whenLoaded('contacto', fn() => [
                'telefono_fijo' => $this->contacto->telefono_fijo,
                'telefono_movil' => $this->contacto->telefono_movil,
                'email' => $this->contacto->email,
            ]),
            'relaciones' => $this->getRelacionesInstitucionales(),
            'created_at' => $this->created_at?->toIso8601String(),
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
                $escalafon = mb_strtolower($movimiento->cupof?->escalafon?->nombre ?? '', 'UTF-8');
                $cargo = mb_strtoupper($movimiento->cupof?->nombre_cargo ?? '', 'UTF-8');

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
                $nombreVinculo = mb_strtoupper($v->pivot?->vinculo?->nombre ?? 'VÍNCULO', 'UTF-8');
                $relaciones[] = $nombreVinculo;
            }
        }

        return array_values(array_unique($relaciones));
    }
}