<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CupofMovimiento extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'cupof_movimientos';

    protected $auditGroup = 'entities';

    protected $fillable = [
        'cupof_id',
        'agente_id',
        'situacion_revista',
        'fecha_inicio',
        'fecha_fin',
        'resolucion',
        'activo'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'activo' => 'boolean'
    ];

    /**
     * Relationship to the CUPOF slot.
     */
    public function cupof(): BelongsTo
    {
        return $this->belongsTo(Cupof::class);
    }

    /**
     * Relationship to the agent.
     */
    public function agente(): BelongsTo
    {
        return $this->belongsTo(Agente::class);
    }
}
