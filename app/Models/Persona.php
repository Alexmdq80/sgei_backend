<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Persona extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    /**
     * Group for segmented auditing.
     *
     * @var string
     */
    protected $auditGroup = 'entities';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "usuario_id",
        "documento_tipo_id",
        "documento_situacion_id",
        "sexo_id",
        "genero_id",
        "nacionalidad_nacion_id",
        "nacion_id",
        "provincia_id",
        "departamento_id",
        "localidad_id",
        "documento_numero",
        "apellido",
        "nombre",
        "nombre_alternativo",
        "tramite",
        "posee_cpi_si",
        "posee_docExt_si",
        "vive_si",
        "CUIL_prefijo",
        "CUIL_sufijo",
        "nacimiento_fecha"
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'nacimiento_fecha' => 'datetime'
    ];

    /**
     * Relationship to the user.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }

    /**
     * Relationship to the document type.
     */
    public function documentoTipo(): BelongsTo
    {
        return $this->belongsTo(DocumentoTipo::class);
    }

    /**
     * Relationship to the document situation.
     */
    public function documentoSituacion(): BelongsTo
    {
        return $this->belongsTo(DocumentoSituacion::class);
    }

    /**
     * Relationship to the sex.
     */
    public function sexo(): BelongsTo
    {
        return $this->belongsTo(Sexo::class);
    }

    /**
     * Relationship to the gender.
     */
    public function genero(): BelongsTo
    {
        return $this->belongsTo(Genero::class);
    }

    /**
     * Relationship to the nationality nation.
     */
    public function nacionalidad(): BelongsTo
    {
        return $this->belongsTo(Nacion::class, "nacionalidad_nacion_id");
    }

    /**
     * Relationship to the country of birth.
     */
    public function nacimientoPais(): BelongsTo
    {
        return $this->belongsTo(Nacion::class, "nacion_id");
    }

    /**
     * Relationship to the province of birth.
     */
    public function nacimientoProvincia(): BelongsTo
    {
        return $this->belongsTo(Provincia::class, "provincia_id");
    }

    /**
     * Relationship to the department of birth.
     */
    public function nacimientoDepartamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class, "departamento_id");
    }

    /**
     * Relationship to the locality of birth.
     */
    public function nacimientoLocalidad(): BelongsTo
    {
        return $this->belongsTo(Localidad::class, "localidad_id");
    }

    /**
     * Relationship to the address.
     */
    public function domicilio(): HasOne
    {
        return $this->hasOne(Domicilio::class);
    }

    /**
     * Relationship to the contact information.
     */
    public function contacto(): HasOne
    {
        return $this->hasOne(Contacto::class);
    }

    /**
     * Relationship to the registration.
     */
    public function inscripcion(): HasOne
    {
        return $this->hasOne(Inscripcion::class, "persona_id", "id");
    }

    /**
     * Relationship to the registration history.
     */
    public function historialInscripciones(): HasMany
    {
        return $this->hasMany(HistorialInscripcion::class, "persona_id", "id");
    }

    /**
     * Relationship to the students files.
     */
    public function legajos(): HasMany
    {
        return $this->hasMany(Legajo::class);
    }

    /**
     * Relationship to CUPOF movements (occupancy of job slots).
     */
    public function movimientosCupof(): HasMany
    {
        return $this->hasMany(CupofMovimiento::class);
    }

    /**
     * Relationship to active CUPOF movements.
     */
    public function movimientosCupofActivos(): HasMany
    {
        return $this->hasMany(CupofMovimiento::class)->where('activo', true);
    }

    /**
     * Relationship to other people as a student.
     */
    public function vinculosComoEstudiante(): BelongsToMany
    {
        return $this->belongsToMany(Persona::class, 'persona_vinculo_persona', 'persona_estudiante_id', 'persona_adulto_id')
                    ->using(PersonaVinculoPersona::class)
                    ->withPivot(['vinculo_id', 'vencimiento_fecha', 'detalle']);
    }

    /**
     * Relationship to other people as an adult.
     */
    public function vinculosComoAdulto(): BelongsToMany
    {
        return $this->belongsToMany(Persona::class, 'persona_vinculo_persona', 'persona_adulto_id', 'persona_estudiante_id')
                    ->using(PersonaVinculoPersona::class)
                    ->withPivot(['vinculo_id', 'vencimiento_fecha', 'detalle']);
    }
}
