<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $historial_inscripcion_id
 * @property int|null $salida_motivo_id
 * @property string|null $otro_motivo
 * @property int $accion_contacto
 * @property int $accion_prevencion
 * @property int $accion_equipo
 * @property int $accion_otros
 * @property int $accion_ninguna
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\HistorialInscripcion|null $historialInscripcion
 * @property-read \App\Models\SalidaMotivo|null $salidaMotivo
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionBaja newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionBaja newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionBaja onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionBaja query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionBaja whereAccionContacto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionBaja whereAccionEquipo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionBaja whereAccionNinguna($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionBaja whereAccionOtros($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionBaja whereAccionPrevencion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionBaja whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionBaja whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionBaja whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionBaja whereHistorialInscripcionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionBaja whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionBaja whereOtroMotivo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionBaja whereSalidaMotivoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionBaja whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionBaja whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionBaja withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionBaja withoutTrashed()
 * @mixin \Eloquent
 */
class InscripcionBaja extends Model
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
        "salida_motivo_id",
        "otro_motivo",
        "accion_contacto",
        "accion_prevencion",
        "accion_equipo",
        "accion_otros",
        "accion_ninguna"
    ];

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
}
