<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
