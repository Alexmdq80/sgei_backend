<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string|null $id_georef
 * @property int|null $nacion_id
 * @property int|null $georef_fuente_id
 * @property int|null $georef_categoria_id
 * @property string $nombre
 * @property string|null $nombre_completo
 * @property string|null $iso_nombre
 * @property string|null $iso_id
 * @property numeric|null $centroide_lat
 * @property numeric|null $centroide_lon
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Departamento> $departamentos
 * @property-read int|null $departamentos_count
 * @property-read \App\Models\GeorefCategoria|null $georefCategoria
 * @property-read \App\Models\GeorefFuente|null $georefFuente
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Municipio> $municipios
 * @property-read int|null $municipios_count
 * @property-read \App\Models\Nacion|null $nacion
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Persona> $personas
 * @property-read int|null $personas_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Region> $regiones
 * @property-read int|null $regiones_count
 * @method static \Database\Factories\ProvinciaFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Provincia newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Provincia newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Provincia onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Provincia query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Provincia whereCentroideLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Provincia whereCentroideLon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Provincia whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Provincia whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Provincia whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Provincia whereGeorefCategoriaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Provincia whereGeorefFuenteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Provincia whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Provincia whereIdGeoref($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Provincia whereIsoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Provincia whereIsoNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Provincia whereNacionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Provincia whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Provincia whereNombreCompleto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Provincia whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Provincia whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Provincia withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Provincia withoutTrashed()
 * @mixin \Eloquent
 */
class Provincia extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "id_georef",
        "nacion_id",
        "georef_fuente_id",
        "georef_categoria_id",
        "nombre",
        "nombre_completo",
        "iso_nombre",
        "iso_id",
        "centroide_lat",
        "centroide_lon"
    ];

    /**
     * Relationship to the nation.
     */
    public function nacion(): BelongsTo
    {
        return $this->belongsTo(Nacion::class);
    }

    /**
     * Relationship to the georef category.
     */
    public function georefCategoria(): BelongsTo
    {
        return $this->belongsTo(GeorefCategoria::class);
    }

    /**
     * Relationship to the georef source.
     */
    public function georefFuente(): BelongsTo
    {
        return $this->belongsTo(GeorefFuente::class);
    }

    /**
     * Relationship to the regions in this province.
     */
    public function regiones(): HasMany
    {
        return $this->hasMany(Region::class);
    }

    /**
     * Relationship to the departments in this province.
     */
    public function departamentos(): HasMany
    {
        return $this->hasMany(Departamento::class);
    }

    /**
     * Relationship to the municipalities in this province.
     */
    public function municipios(): HasMany
    {
        return $this->hasMany(Municipio::class);
    }

    /**
     * Relationship to the people associated with this province.
     */
    public function personas(): HasMany
    {
        return $this->hasMany(Persona::class);
    }
}
