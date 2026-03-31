<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inscripcion extends Model
{
    use HasFactory, HasUuids, SoftDeletes, AuditableTrait;

    /**
     * Group for segmented auditing.
     *
     * @var string
     */
    protected $auditGroup = 'academic';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "persona_id",
        "persona_firma_id",
        "espacio_id",
        "escuela_id",
        "nivel_id",
        "modalidad_id",
        "condicion_id",
        "persona_vinculo_persona_1_id",
        "persona_vinculo_persona_2_id",
        "persona_vinculo_persona_3_id",
        "codigo_abc",
        "proyecto_inclusion_si",
        "concurre_especial_si",
        "asistente_externo_si",
        "fecha"
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha' => 'datetime'
    ];

    /**
     * Relationship to the student (Persona).
     */
    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, "persona_id", "id");
    }

    /**
     * Relationship to the person who signed the registration (Persona).
     */
    public function personaFirma(): BelongsTo
    {
        return $this->belongsTo(Persona::class, "persona_firma_id", "id");
    }

    /**
     * Relationship to the academic space.
     */
    public function espacio(): BelongsTo
    {
        return $this->belongsTo(Espacio::class);
    }

    /**
     * Relationship to the school of origin.
     */
    public function escuelaProcedencia(): BelongsTo
    {
        return $this->belongsTo(Escuela::class, "escuela_id");
    }

    /**
     * Relationship to the level of origin.
     */
    public function nivelProcedencia(): BelongsTo
    {
        return $this->belongsTo(Nivel::class, "nivel_id");
    }

    /**
     * Relationship to the modality of origin.
     */
    public function modalidadProcedencia(): BelongsTo
    {
        return $this->belongsTo(Modalidad::class, "modalidad_id");
    }

    /**
     * Relationship to the registration condition.
     */
    public function condicion(): BelongsTo
    {
        return $this->belongsTo(Condicion::class);
    }

    /**
     * Relationship to the first vinculated person.
     */
    public function vinculoPersona_1(): BelongsTo
    {
        return $this->belongsTo(PersonaVinculoPersona::class, "persona_vinculo_persona_1_id", "id");
    }

    /**
     * Relationship to the second vinculated person.
     */
    public function vinculoPersona_2(): BelongsTo
    {
        return $this->belongsTo(PersonaVinculoPersona::class, "persona_vinculo_persona_2_id", "id");
    }

    /**
     * Relationship to the third vinculated person.
     */
    public function vinculoPersona_3(): BelongsTo
    {
        return $this->belongsTo(PersonaVinculoPersona::class, "persona_vinculo_persona_3_id", "id");
    }

    /**
     * Relationship to the registration history records.
     */
    public function historial(): HasMany
    {
        return $this->hasMany(HistorialInscripcion::class);
    }
}
