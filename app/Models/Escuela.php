<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int|null $localidad_id
 * @property int|null $ambito_id
 * @property int|null $dependencia_id
 * @property int|null $sector_id
 * @property string|null $cue_anexo
 * @property string|null $clave_provincial
 * @property string $nombre
 * @property string|null $numero
 * @property string|null $codigo_localidad
 * @property string|null $domicilio
 * @property string|null $telefono
 * @property string|null $email
 * @property string|null $codigo_postal
 * @property int $modalidad_comun
 * @property int $modalidad_especial
 * @property int $modalidad_adultos
 * @property int $comun_inicial_maternal
 * @property int $comun_inicial_infantes
 * @property int $comun_primario
 * @property int $comun_secundario
 * @property int $comun_secundario_inet
 * @property int $comun_snu
 * @property int $comun_snu_inet
 * @property int $comun_snu_cursos
 * @property int $especial_temprana
 * @property int $especial_inicial
 * @property int $especial_primario
 * @property int $especial_secundario
 * @property int $especial_integracion
 * @property int $adultos_primario
 * @property int $adultos_secundario
 * @property int $adultos_profesional
 * @property int $adultos_profesional_inet
 * @property int $adultos_alfabetizacion
 * @property int $hospitalario_inicial
 * @property int $hospitalario_primario
 * @property int $hospitalario_secundario
 * @property int $talleres_artistica
 * @property int $servicios_complementarios
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Ambito|null $ambito
 * @property-read \App\Models\Dependencia|null $dependencia
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EscuelaPersona> $escuelasPersonas
 * @property-read int|null $escuelas_personas_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InscripcionPase> $inscripcionPases
 * @property-read int|null $inscripcion_pases_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Inscripcion> $inscripcionProcedencias
 * @property-read int|null $inscripcion_procedencias_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Legajo> $legajos
 * @property-read int|null $legajos_count
 * @property-read \App\Models\Localidad|null $localidad
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ModalidadNivel> $modalidadesNiveles
 * @property-read int|null $modalidades_niveles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Oferta> $ofertas
 * @property-read int|null $ofertas_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Propuesta> $propuestas
 * @property-read int|null $propuestas_count
 * @property-read \App\Models\Sector|null $sector
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Usuario> $usuarios
 * @property-read int|null $usuarios_count
 * @method static \Database\Factories\EscuelaFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereAdultosAlfabetizacion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereAdultosPrimario($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereAdultosProfesional($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereAdultosProfesionalInet($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereAdultosSecundario($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereAmbitoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereClaveProvincial($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereCodigoLocalidad($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereCodigoPostal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereComunInicialInfantes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereComunInicialMaternal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereComunPrimario($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereComunSecundario($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereComunSecundarioInet($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereComunSnu($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereComunSnuCursos($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereComunSnuInet($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereCueAnexo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereDependenciaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereDomicilio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereEspecialInicial($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereEspecialIntegracion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereEspecialPrimario($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereEspecialSecundario($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereEspecialTemprana($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereHospitalarioInicial($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereHospitalarioPrimario($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereHospitalarioSecundario($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereLocalidadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereModalidadAdultos($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereModalidadComun($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereModalidadEspecial($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereNumero($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereSectorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereServiciosComplementarios($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereTalleresArtistica($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereTelefono($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela withoutTrashed()
 * @mixin \Eloquent
 */
class Escuela extends Model
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
        "localidad_id",
        "ambito_id",
        "dependencia_id",
        "sector_id",
        "cue_anexo",
        "clave_provincial",
        "nombre",
        "numero",
        "codigo_localidad",
        "domicilio",
        "telefono",
        "email",
        "codigo_postal",
        "modalidad_comun",
        "modalidad_especial",
        "modalidad_adultos",
        "comun_inicial_maternal",
        "comun_inicial_infantes",
        "comun_primario",
        "comun_secundario",
        "comun_secundario_inet",
        "comun_snu",
        "comun_snu_inet",
        "comun_snu_cursos",
        "especial_temprana",
        "especial_inicial",
        "especial_primario",
        "especial_secundario",
        "especial_integracion",
        "adultos_primario",
        "adultos_secundario",
        "adultos_profesional",
        "adultos_profesional_inet",
        "adultos_alfabetizacion",
        "hospitalario_inicial",
        "hospitalario_primario",
        "hospitalario_secundario",
        "talleres_artistica",
        "servicios_complementarios",
        "created_by",
        "updated_by"
    ];

    /**
     * Relationship to the locality.
     */
    public function localidad(): BelongsTo
    {
        return $this->belongsTo(Localidad::class);
    }

    /**
     * Relationship to the ambit.
     */
    public function ambito(): BelongsTo
    {
        return $this->belongsTo(Ambito::class);
    }

    /**
     * Relationship to the dependency.
     */
    public function dependencia(): BelongsTo
    {
        return $this->belongsTo(Dependencia::class);
    }

    /**
     * Relationship to the sector.
     */
    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class);
    }

    /**
     * Relationship to the registration origin records.
     */
    public function inscripcionProcedencias(): HasMany
    {
        return $this->hasMany(Inscripcion::class);
    }

    /**
     * Relationship to the transfer registration records.
     */
    public function inscripcionPases(): HasMany
    {
        return $this->hasMany(InscripcionPase::class);
    }

    /**
     * Relationship to the students files (Legajos).
     */
    public function legajos(): HasMany
    {
        return $this->hasMany(Legajo::class);
    }

    /**
     * Relationship to the institutional proposals.
     */
    public function propuestas(): HasMany
    {
        return $this->hasMany(Propuesta::class);
    }

    /**
     * Relationship to the users associated with the school via the pivot model.
     */
    public function escuelasPersonas(): HasMany
    {
        return $this->hasMany(EscuelaPersona::class);
    }

    /**
     * Relationship to the users associated with the school.
     */
    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(Usuario::class)
                    ->withPivot(['usuario_tipo_id', 'verified_at']);
    }

    /**
     * Relationship to the modalities and levels associated with the school.
     */
    public function modalidadesNiveles(): BelongsToMany
    {
        return $this->belongsToMany(ModalidadNivel::class, 'escuela_modalidad_nivel', 'escuela_id', 'modalidad_nivel_id');
    }

    /**
     * Relationship to the offers associated with the school.
     */
    public function ofertas(): BelongsToMany
    {
        return $this->belongsToMany(Oferta::class);
    }
}
