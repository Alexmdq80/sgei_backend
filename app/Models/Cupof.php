<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Cupof extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $auditGroup = 'entities';

    protected $fillable = [
        'codigo_cupof',
        'escuela_id',
        'asignatura_id',
        'escalafon',
        'tipo_puesto',
        'cantidad',
        'estado_cupof',
        'motivo_baja'
    ];

    /**
     * Relationship to the school.
     */
    public function escuela(): BelongsTo
    {
        return $this->belongsTo(Escuela::class);
    }

    /**
     * Relationship to the subject (only for docente escalafon).
     */
    public function asignatura(): BelongsTo
    {
        return $this->belongsTo(Asignatura::class);
    }

    /**
     * Relationship to all movements.
     */
    public function movimientos(): HasMany
    {
        return $this->hasMany(CupofMovimiento::class);
    }

    /**
     * Relationship to the current active movement (occupant).
     */
    public function movimientoActivo(): HasOne
    {
        return $this->hasOne(CupofMovimiento::class)->where('activo', true);
    }
}
