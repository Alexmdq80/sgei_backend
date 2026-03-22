<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Espacio extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "propuesta_id",
        "seccion_tipo_id",
        "division",
        "nombre"
    ];

    /**
     * Relationship to the proposal.
     */
    public function propuesta(): BelongsTo
    {
        return $this->belongsTo(Propuesta::class);
    }

    /**
     * Relationship to the section type.
     */
    public function seccionTipo(): BelongsTo
    {
        return $this->belongsTo(SeccionTipo::class, "seccion_tipo_id");
    }

    /**
     * Relationship to the registrations.
     */
    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class);
    }

    /**
     * Relationship to the registration history records.
     */
    public function historialInscripciones(): HasMany
    {
        return $this->hasMany(HistorialInscripcion::class);
    }
}
