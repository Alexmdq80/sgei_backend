<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property string $inscripcion_id
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
 * @property-read \App\Models\InscripcionBaja|null $baja
 * @property-read \App\Models\Condicion|null $condicion
 * @property-read \App\Models\Escuela|null $escuelaProcedencia
 * @property-read \App\Models\Espacio|null $espacio
 * @property-read \App\Models\InscripcionFinalizado|null $finalizado
 * @property-read \App\Models\HistorialInfoInscripcion|null $info
 * @property-read \App\Models\Inscripcion|null $inscripcion
 * @property-read \App\Models\Modalidad|null $modalidadProcedencia
 * @property-read \App\Models\Nivel|null $nivelProcedencia
 * @property-read \App\Models\InscripcionPase|null $pase
 * @property-read \App\Models\Persona|null $persona
 * @property-read \App\Models\Persona|null $personaFirma
 * @property-read \App\Models\PersonaVinculoPersona|null $vinculoPersona_1
 * @property-read \App\Models\PersonaVinculoPersona|null $vinculoPersona_2
 * @property-read \App\Models\PersonaVinculoPersona|null $vinculoPersona_3
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion whereAsistenteExternoSi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion whereCodigoAbc($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion whereConcurreEspecialSi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion whereCondicionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion whereEscuelaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion whereEspacioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion whereFecha($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion whereInscripcionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion whereModalidadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion whereNivelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion wherePersonaFirmaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion wherePersonaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion wherePersonaVinculoPersona1Id($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion wherePersonaVinculoPersona2Id($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion wherePersonaVinculoPersona3Id($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion whereProyectoInclusionSi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion withoutTrashed()
 * @mixin \Eloquent
 */
class HistorialInscripcion extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $auditGroup = "academic";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "inscripcion_id",
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
     * Relationship to the original registration.
     */
    public function inscripcion(): BelongsTo
    {
        return $this->belongsTo(Inscripcion::class);
    }

    /**
     * Relationship to the person (student).
     */
    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }

    /**
     * Relationship to the person who signed.
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
     * Relationship to the finalized record.
     */
    public function finalizado(): HasOne
    {
        return $this->hasOne(InscripcionFinalizado::class);
    }

    /**
     * Relationship to the transfer record.
     */
    public function pase(): HasOne
    {
        return $this->hasOne(InscripcionPase::class);
    }

    /**
     * Relationship to the cancel record.
     */
    public function baja(): HasOne
    {
        return $this->hasOne(InscripcionBaja::class);
    }

    /**
     * Relationship to the extra information.
     */
    public function info(): HasOne
    {
        return $this->hasOne(HistorialInfoInscripcion::class);
    }
}
