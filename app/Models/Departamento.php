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
 * @property int|null $provincia_id
 * @property int|null $georef_fuente_id
 * @property int|null $georef_categoria_id
 * @property string $nombre
 * @property string|null $nombre_completo
 * @property numeric|null $centroide_lat
 * @property numeric|null $centroide_lon
 * @property string|null $provincia_interseccion
 * @property int|null $region_id
 * @property int|null $distrito_numero
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GeorefAsentamiento> $georefAsentamientos
 * @property-read int|null $georef_asentamientos_count
 * @property-read \App\Models\GeorefCategoria|null $georefCategoria
 * @property-read \App\Models\GeorefFuente|null $georefFuente
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GeorefLocalidad> $georefLocalidades
 * @property-read int|null $georef_localidades_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Localidad> $localidades
 * @property-read int|null $localidades_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Persona> $personas
 * @property-read int|null $personas_count
 * @property-read \App\Models\Provincia|null $provincia
 * @property-read \App\Models\Region|null $region
 * @method static \Database\Factories\DepartamentoFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento whereCentroideLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento whereCentroideLon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento whereDistritoNumero($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento whereGeorefCategoriaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento whereGeorefFuenteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento whereIdGeoref($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento whereNombreCompleto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento whereProvinciaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento whereProvinciaInterseccion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento whereRegionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento withoutTrashed()
 * @mixin \Eloquent
 */
class Departamento extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "id_georef",
        "provincia_id",
        "georef_fuente",
        "georef_categoria",
        "nombre",
        "nombre_completo",
        "centroide_lat",
        "centroide_lon",
        "provincia_interseccion",
        "region_id",
        "distrito_numero"
    ];

    /**
     * Relationship to the province.
     */
    public function provincia(): BelongsTo
    {
        return $this->belongsTo(Provincia::class);
    }

    /**
     * Relationship to the educational region.
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
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
     * Relationship to the localities.
     */
    public function localidades(): HasMany
    {
        return $this->hasMany(Localidad::class);
    }

    /**
     * Relationship to the georef localities.
     */
    public function georefLocalidades(): HasMany
    {
        return $this->hasMany(GeorefLocalidad::class);
    }

    /**
     * Relationship to the georef settlements.
     */
    public function georefAsentamientos(): HasMany
    {
        return $this->hasMany(GeorefAsentamiento::class);
    }

    /**
     * Relationship to the people associated with the department.
     */
    public function personas(): HasMany
    {
        return $this->hasMany(Persona::class);
    }
}
