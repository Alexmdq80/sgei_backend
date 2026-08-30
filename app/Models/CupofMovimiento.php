<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $cupof_id
 * @property int $persona_id
 * @property string $situacion_revista
 * @property \Illuminate\Support\Carbon $fecha_inicio
 * @property \Illuminate\Support\Carbon|null $fecha_fin
 * @property string|null $resolucion
 * @property bool $activo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Cupof|null $cupof
 * @property-read \App\Models\Persona|null $persona
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CupofMovimiento newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CupofMovimiento newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CupofMovimiento onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CupofMovimiento query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CupofMovimiento whereActivo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CupofMovimiento whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CupofMovimiento whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CupofMovimiento whereCupofId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CupofMovimiento whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CupofMovimiento whereFechaFin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CupofMovimiento whereFechaInicio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CupofMovimiento whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CupofMovimiento wherePersonaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CupofMovimiento whereResolucion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CupofMovimiento whereSituacionRevista($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CupofMovimiento whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CupofMovimiento whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CupofMovimiento withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CupofMovimiento withoutTrashed()
 * @mixin \Eloquent
 */
class CupofMovimiento extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'cupof_movimientos';

    protected $auditGroup = 'entities';

    protected $fillable = [
        'cupof_id',
        'persona_id',
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
     * Relationship to the persona (identity).
     */
    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }
}
