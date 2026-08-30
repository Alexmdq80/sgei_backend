<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property string $codigo_cupof
 * @property int $escuela_id
 * @property int|null $asignatura_id
 * @property int $escalafon_id
 * @property int $puesto_tipo_id
 * @property string|null $nombre_cargo
 * @property int $cantidad
 * @property string $estado_cupof
 * @property string|null $motivo_baja
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Asignatura|null $asignatura
 * @property-read \App\Models\Escalafon|null $escalafon
 * @property-read \App\Models\Escuela|null $escuela
 * @property-read \App\Models\CupofMovimiento|null $movimientoActivo
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CupofMovimiento> $movimientos
 * @property-read int|null $movimientos_count
 * @property-read \App\Models\PuestoTipo|null $puestoTipo
 * @method static \Database\Factories\CupofFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cupof newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cupof newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cupof onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cupof query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cupof whereAsignaturaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cupof whereCantidad($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cupof whereCodigoCupof($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cupof whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cupof whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cupof whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cupof whereEscalafonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cupof whereEscuelaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cupof whereEstadoCupof($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cupof whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cupof whereMotivoBaja($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cupof whereNombreCargo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cupof wherePuestoTipoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cupof whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cupof whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cupof withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cupof withoutTrashed()
 * @mixin \Eloquent
 */
class Cupof extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $auditGroup = 'entities';

    protected $fillable = [
        'codigo_cupof',
        'escuela_id',
        'asignatura_id',
        'escalafon_id',
        'puesto_tipo_id',
        'nombre_cargo',
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
     * Relationship to the shift type.
     */
    public function escalafon(): BelongsTo
    {
        return $this->belongsTo(Escalafon::class);
    }

    /**
     * Relationship to the position type.
     */
    public function puestoTipo(): BelongsTo
    {
        return $this->belongsTo(PuestoTipo::class);
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
