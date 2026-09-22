<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $historial_inscripcion_id
 * @property int|null $cierre_causa_id
 * @property Carbon|null $fecha
 * @property string|null $observaciones
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read CierreCausa|null $cierreCausa
 * @property-read HistorialInscripcion|null $historialInscripcion
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInfoInscripcion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInfoInscripcion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInfoInscripcion onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInfoInscripcion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInfoInscripcion whereCierreCausaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInfoInscripcion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInfoInscripcion whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInfoInscripcion whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInfoInscripcion whereFecha($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInfoInscripcion whereHistorialInscripcionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInfoInscripcion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInfoInscripcion whereObservaciones($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInfoInscripcion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInfoInscripcion whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInfoInscripcion withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInfoInscripcion withoutTrashed()
 *
 * @mixin \Eloquent
 */
class HistorialInfoInscripcion extends Model
{
    use AuditableTrait, HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'historial_inscripcion_id',
        'cierre_causa_id',
        'fecha',
        'observaciones',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha' => 'datetime',
    ];

    /**
     * Relationship to the history record.
     */
    public function historialInscripcion(): BelongsTo
    {
        return $this->belongsTo(HistorialInscripcion::class);
    }

    /**
     * Relationship to the closure cause.
     */
    public function cierreCausa(): BelongsTo
    {
        return $this->belongsTo(CierreCausa::class);
    }
}
