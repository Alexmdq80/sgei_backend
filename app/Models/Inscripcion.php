<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property int $persona_id
 * @property int|null $persona_firma_id
 * @property int|null $espacio_id
 * @property int|null $escuela_id
 * @property int|null $nivel_id
 * @property int|null $modalidad_id
 * @property int|null $condicion_id
 * @property int|null $persona_vinculo_persona_1_id
 * @property int|null $persona_vinculo_persona_2_id
 * @property int|null $persona_vinculo_persona_3_id
 * @property string|null $codigo_abc
 * @property int $proyecto_inclusion_si
 * @property int $concurre_especial_si
 * @property int $asistente_externo_si
 * @property \Illuminate\Support\Carbon|null $fecha
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Condicion|null $condicion
 * @property-read \App\Models\Escuela|null $escuelaProcedencia
 * @property-read \App\Models\Espacio|null $espacio
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HistorialInscripcion> $historial
 * @property-read int|null $historial_count
 * @property-read \App\Models\Modalidad|null $modalidadProcedencia
 * @property-read \App\Models\Nivel|null $nivelProcedencia
 * @property-read \App\Models\Persona|null $persona
 * @property-read \App\Models\Persona|null $personaFirma
 * @property-read \App\Models\PersonaVinculoPersona|null $vinculoPersona_1
 * @property-read \App\Models\PersonaVinculoPersona|null $vinculoPersona_2
 * @property-read \App\Models\PersonaVinculoPersona|null $vinculoPersona_3
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion whereAsistenteExternoSi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion whereCodigoAbc($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion whereConcurreEspecialSi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion whereCondicionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion whereEscuelaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion whereEspacioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion whereFecha($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion whereModalidadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion whereNivelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion wherePersonaFirmaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion wherePersonaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion wherePersonaVinculoPersona1Id($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion wherePersonaVinculoPersona2Id($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion wherePersonaVinculoPersona3Id($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion whereProyectoInclusionSi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion withoutTrashed()
 * @mixin \Eloquent
 */
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
