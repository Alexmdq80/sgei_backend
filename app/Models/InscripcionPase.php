<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
