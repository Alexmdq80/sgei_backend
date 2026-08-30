<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $historial_inscripcion_id
 * @property int|null $condicion_id
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Condicion|null $condicionFinalizacion
 * @property-read \App\Models\HistorialInscripcion|null $historialInscripcion
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionFinalizado newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionFinalizado newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionFinalizado onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionFinalizado query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionFinalizado whereCondicionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionFinalizado whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionFinalizado whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionFinalizado whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionFinalizado whereHistorialInscripcionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionFinalizado whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionFinalizado whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionFinalizado whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionFinalizado withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionFinalizado withoutTrashed()
 * @mixin \Eloquent
 */
class InscripcionFinalizado extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $auditGroup = "academic";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "historial_inscripcion_id",
        "condicion_id"
    ];

    /**
     * Relationship to the history record.
     */
    public function historialInscripcion(): BelongsTo
    {
        return $this->belongsTo(HistorialInscripcion::class);
    }

    /**
     * Relationship to the condition.
     */
    public function condicionFinalizacion(): BelongsTo
    {
        return $this->belongsTo(Condicion::class, "condicion_id");
    }
}
