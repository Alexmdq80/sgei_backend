<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Casts\DocumentoIdentidadCast;

/**
 * @property int $id
 * @property string|null $usuario_id
 * @property int|null $documento_tipo_id
 * @property int|null $documento_situacion_id
 * @property int|null $sexo_id
 * @property int|null $genero_id
 * @property int|null $nacionalidad_nacion_id
 * @property int|null $nacion_id
 * @property int|null $provincia_id
 * @property int|null $departamento_id
 * @property int|null $localidad_id
 * @property \App\ValueObjects\DocumentoIdentidad|null $documento_numero
 * @property string|null $apellido
 * @property string|null $nombre
 * @property string|null $foto_path
 * @property string|null $nombre_alternativo
 * @property string|null $tramite
 * @property int|null $vive_si
 * @property string|null $CUIL_prefijo
 * @property string|null $CUIL_sufijo
 * @property \Illuminate\Support\Carbon|null $nacimiento_fecha
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Contacto|null $contacto
 * @property-read \App\Models\DocumentoSituacion|null $documentoSituacion
 * @property-read \App\Models\DocumentoTipo|null $documentoTipo
 * @property-read \App\Models\Domicilio|null $domicilio
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EscuelaPersona> $escuelasPersonas
 * @property-read int|null $escuelas_personas_count
 * @property-read \App\Models\Genero|null $genero
 * @property-read string|null $foto_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HistorialInscripcion> $historialInscripciones
 * @property-read int|null $historial_inscripciones_count
 * @property-read \App\Models\Inscripcion|null $inscripcion
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Legajo> $legajos
 * @property-read int|null $legajos_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CupofMovimiento> $movimientosCupof
 * @property-read int|null $movimientos_cupof_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CupofMovimiento> $movimientosCupofActivos
 * @property-read int|null $movimientos_cupof_activos_count
 * @property-read \App\Models\Departamento|null $nacimientoDepartamento
 * @property-read \App\Models\Localidad|null $nacimientoLocalidad
 * @property-read \App\Models\Nacion|null $nacimientoPais
 * @property-read \App\Models\Provincia|null $nacimientoProvincia
 * @property-read \App\Models\Nacion|null $nacionalidad
 * @property-read \App\Models\Sexo|null $sexo
 * @property-read \App\Models\Usuario|null $usuario
 * @property-read \App\Models\PersonaVinculoPersona|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Persona> $vinculosComoAdulto
 * @property-read int|null $vinculos_como_adulto_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Persona> $vinculosComoEstudiante
 * @property-read int|null $vinculos_como_estudiante_count
 * @method static \Database\Factories\PersonaFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona inDepartamento(int $departamentoId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona inProvincia(int $provinciaId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona inRegion(int $regionId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereApellido($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereCUILPrefijo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereCUILSufijo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereDepartamentoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereDocumentoNumero($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereDocumentoSituacionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereDocumentoTipoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereFotoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereGeneroId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereLocalidadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereNacimientoFecha($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereNacionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereNacionalidadNacionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereNombreAlternativo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereProvinciaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereSexoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereTramite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereUsuarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereViveSi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona withoutTrashed()
 * @mixin \Eloquent
 */
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
        "foto_path",
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
        'documento_numero' => DocumentoIdentidadCast::class,
        'nacimiento_fecha' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = ['foto_url'];

    /**
     * Mutator for apellido (Uppercase).
     */
    protected function apellido(): Attribute
    {
        return Attribute::make(
            set: fn(?string $value) => $value ? mb_strtoupper(trim($value), 'UTF-8') : null,
        );
    }

    /**
     * Mutator for nombre (Uppercase).
     */
    protected function nombre(): Attribute
    {
        return Attribute::make(
            set: fn(?string $value) => $value ? mb_strtoupper(trim($value), 'UTF-8') : null,
        );
    }

    /**
     * Mutator for nombre_alternativo (Uppercase).
     */
    protected function nombreAlternativo(): Attribute
    {
        return Attribute::make(
            set: fn(?string $value) => $value ? mb_strtoupper(trim($value), 'UTF-8') : null,
        );
    }

    /**
     * Mutator for tramite (Uppercase).
     */
    protected function tramite(): Attribute
    {
        return Attribute::make(
            set: fn(?string $value) => $value ? mb_strtoupper(trim($value), 'UTF-8') : null,
        );
    }
    /**
     * Get the full URL to the (private, authenticated) profile photo.
     */
    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto_path ? url("/api/v1/admin/personas/{$this->id}/foto") : null;
    }
    /**
     * Relationship to the user.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }
    /**
     * Relationship to school associations via the pivot model.
     */
    public function escuelasPersonas(): HasMany
    {
        return $this->hasMany(EscuelaPersona::class);
    }

    /**
     * Returns the raw documento_numero string from the database,
     * bypassing the DocumentoIdentidadCast Value Object.
     * Use this whenever you need to query or compare against the raw column value.
     */
    public function documentoNumeroRaw(): ?string
    {
        return $this->getRawOriginal('documento_numero');
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

    /**
     * Scope: Personas dentro de una provincia (vía CUPOF, inscripción o vínculos).
     */
    public function scopeInProvincia($query, int $provinciaId): void
    {
        $query->where(function ($q) use ($provinciaId) {
            $q->whereHas('movimientosCupofActivos.cupof.escuela.localidad.departamento', function ($sq) use ($provinciaId) {
                $sq->where('provincia_id', $provinciaId);
            })
                ->orWhereHas('inscripcion.escuelaProcedencia.localidad.departamento', function ($sq) use ($provinciaId) {
                    $sq->where('provincia_id', $provinciaId);
                })
                ->orWhereHas('vinculosComoAdulto.inscripcion.escuelaProcedencia.localidad.departamento', function ($sq) use ($provinciaId) {
                    $sq->where('provincia_id', $provinciaId);
                });
        });
    }

    /**
     * Scope: Personas dentro de una región (vía CUPOF, inscripción o vínculos).
     */
    public function scopeInRegion($query, int $regionId): void
    {
        $query->where(function ($q) use ($regionId) {
            $q->whereHas('movimientosCupofActivos.cupof.escuela.localidad.departamento', function ($sq) use ($regionId) {
                $sq->where('region_id', $regionId);
            })
                ->orWhereHas('inscripcion.escuelaProcedencia.localidad.departamento', function ($sq) use ($regionId) {
                    $sq->where('region_id', $regionId);
                })
                ->orWhereHas('vinculosComoAdulto.inscripcion.escuelaProcedencia.localidad.departamento', function ($sq) use ($regionId) {
                    $sq->where('region_id', $regionId);
                });
        });
    }

    /**
     * Scope: Personas dentro de un departamento (vía CUPOF, inscripción o vínculos).
     */
    public function scopeInDepartamento($query, int $departamentoId): void
    {
        $query->where(function ($q) use ($departamentoId) {
            $q->whereHas('movimientosCupofActivos.cupof.escuela.localidad', function ($sq) use ($departamentoId) {
                $sq->where('departamento_id', $departamentoId);
            })
                ->orWhereHas('inscripcion.escuelaProcedencia.localidad', function ($sq) use ($departamentoId) {
                    $sq->where('departamento_id', $departamentoId);
                })
                ->orWhereHas('vinculosComoAdulto.inscripcion.escuelaProcedencia.localidad', function ($sq) use ($departamentoId) {
                    $sq->where('departamento_id', $departamentoId);
                });
        });
    }

}
