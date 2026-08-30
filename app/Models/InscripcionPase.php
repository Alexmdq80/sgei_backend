<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $escuela_id
 * @property int $historial_inscripcion_id
 * @property int|null $salida_motivo_id
 * @property int|null $escuela_ubicacion_id
 * @property string|null $otro_motivo
 * @property int $finalizado
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Escuela|null $escuela
 * @property-read \App\Models\EscuelaUbicacion|null $escuelaUbicacion
 * @property-read \App\Models\HistorialInscripcion|null $historialInscripcion
 * @property-read \App\Models\SalidaMotivo|null $salidaMotivo
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionPase newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionPase newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionPase onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionPase query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionPase whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionPase whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionPase whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionPase whereEscuelaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionPase whereEscuelaUbicacionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionPase whereFinalizado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionPase whereHistorialInscripcionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionPase whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionPase whereOtroMotivo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionPase whereSalidaMotivoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionPase whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionPase whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionPase withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionPase withoutTrashed()
 * @mixin \Eloquent
 */
class InscripcionPase extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $auditGroup = "academic";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "escuela_id",
        "historial_inscripcion_id",
        "salida_motivo_id",
        "escuela_ubicacion_id",
        "otro_motivo",
        "finalizado"
    ];

    /**
     * Relationship to the destination school.
     */
    public function escuela(): BelongsTo
    {
        return $this->belongsTo(Escuela::class);
    }

    /**
     * Relationship to the history record.
     */
    public function historialInscripcion(): BelongsTo
    {
        return $this->belongsTo(HistorialInscripcion::class);
    }

    /**
     * Relationship to the exit reason.
     */
    public function salidaMotivo(): BelongsTo
    {
        return $this->belongsTo(SalidaMotivo::class);
    }

    /**
     * Relationship to the school location type.
     */
    public function escuelaUbicacion(): BelongsTo
    {
        return $this->belongsTo(EscuelaUbicacion::class);
    }
}
