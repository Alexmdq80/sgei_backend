<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $nombre
 * @property int|null $orden
 * @property int $vigente
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GeorefAsentamiento> $asentamientos
 * @property-read int|null $asentamientos_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Calle> $calles
 * @property-read int|null $calles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Departamento> $departamentos
 * @property-read int|null $departamentos_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Localidad> $localidades
 * @property-read int|null $localidades_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LocalidadCensal> $localidadesCensales
 * @property-read int|null $localidades_censales_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Municipio> $municipios
 * @property-read int|null $municipios_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Provincia> $provincias
 * @property-read int|null $provincias_count
 * @method static \Database\Factories\GeorefCategoriaFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefCategoria newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefCategoria newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefCategoria onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefCategoria query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefCategoria whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefCategoria whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefCategoria whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefCategoria whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefCategoria whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefCategoria whereOrden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefCategoria whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefCategoria whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefCategoria whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefCategoria withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefCategoria withoutTrashed()
 * @mixin \Eloquent
 */
class GeorefCategoria extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "nombre",
        "orden",
        "vigente"
    ];

    /**
     * Relationship to the streets.
     */
    public function calles(): HasMany
    {
        return $this->hasMany(Calle::class);
    }

    /**
     * Relationship to the settlements.
     */
    public function asentamientos(): HasMany
    {
        return $this->hasMany(GeorefAsentamiento::class);
    }

    /**
     * Relationship to the localities.
     */
    public function localidades(): HasMany
    {
        return $this->hasMany(Localidad::class);
    }

    /**
     * Relationship to the census localities.
     */
    public function localidadesCensales(): HasMany
    {
        return $this->hasMany(LocalidadCensal::class);
    }

    /**
     * Relationship to the municipalities.
     */
    public function municipios(): HasMany
    {
        return $this->hasMany(Municipio::class);
    }

    /**
     * Relationship to the departments.
     */
    public function departamentos(): HasMany
    {
        return $this->hasMany(Departamento::class);
    }

    /**
     * Relationship to the provinces.
     */
    public function provincias(): HasMany
    {
        return $this->hasMany(Provincia::class);
    }
}
