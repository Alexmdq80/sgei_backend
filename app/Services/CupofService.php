<?php

namespace App\Services;

use App\Models\Agente;
use App\Models\Cupof;
use App\Models\CupofMovimiento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class CupofService
{
    /**
     * Get all CUPOFs with their current occupant and relations.
     */
    public function getAllCupofs(array $filters = []): Collection
    {
        $query = Cupof::with(['escuela', 'asignatura', 'movimientoActivo.agente.persona']);

        if (isset($filters['escuela_id'])) {
            $query->where('escuela_id', $filters['escuela_id']);
        }

        if (isset($filters['estado_cupof'])) {
            $query->where('estado_cupof', $filters['estado_cupof']);
        }

        if (isset($filters['escalafon'])) {
            $query->where('escalafon', $filters['escalafon']);
        }

        return $query->get();
    }

    /**
     * Create a new CUPOF slot.
     */
    public function createCupof(array $data): Cupof
    {
        return Cupof::create([
            'codigo_cupof' => $data['codigo_cupof'],
            'escuela_id' => $data['escuela_id'],
            'asignatura_id' => $data['asignatura_id'] ?? null,
            'escalafon' => $data['escalafon'],
            'tipo_puesto' => $data['tipo_puesto'],
            'cantidad' => $data['cantidad'] ?? 1,
            'estado_cupof' => 'disponible',
        ]);
    }

    /**
     * Assign an agent to a CUPOF slot.
     */
    public function assignAgente(Cupof $cupof, Agente $agente, array $details): CupofMovimiento
    {
        return DB::transaction(function () use ($cupof, $agente, $details) {
            // 1. Deactivate any current active movement just in case
            $cupof->movimientos()->where('activo', true)->update(['activo' => false, 'fecha_fin' => now()]);

            // 2. Create the new movement
            $movimiento = CupofMovimiento::create([
                'cupof_id' => $cupof->id,
                'agente_id' => $agente->id,
                'situacion_revista' => $details['situacion_revista'],
                'fecha_inicio' => $details['fecha_inicio'] ?? now(),
                'resolucion' => $details['resolucion'] ?? null,
                'activo' => true,
            ]);

            // 3. Update CUPOF status
            $cupof->update(['estado_cupof' => 'ocupado']);

            return $movimiento;
        });
    }

    /**
     * Release a CUPOF slot (e.g. resignation or section closure).
     */
    public function releaseCupof(Cupof $cupof, ?string $motivoBaja = null): bool
    {
        return DB::transaction(function () use ($cupof, $motivoBaja) {
            // 1. Deactivate current occupant
            $cupof->movimientos()->where('activo', true)->update([
                'activo' => false, 
                'fecha_fin' => now()
            ]);

            // 2. Update CUPOF status
            $status = $motivoBaja ? 'baja' : 'disponible';
            return $cupof->update([
                'estado_cupof' => $status,
                'motivo_baja' => $motivoBaja
            ]);
        });
    }
}
