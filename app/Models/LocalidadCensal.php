<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string|null $id_georef
 * @property int|null $georef_fuente_id
 * @property int|null $georef_categoria_id
 * @property int|null $georef_funcion_id
 * @property string $nombre
 * @property numeric|null $centroide_lat
 * @property numeric|null $centroide_lon
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Calle> $calles
 * @property-read int|null $calles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GeorefAsentamiento> $georefAsentamientos
 * @property-read int|null $georef_asentamientos_count
 * @property-read \App\Models\GeorefCategoria|null $georefCategoria
 * @property-read \App\Models\GeorefFuente|null $georefFuente
 * @property-read \App\Models\GeorefFuncion|null $georefFuncion
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GeorefLocalidad> $georefLocalidades
 * @property-read int|null $georef_localidades_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Localidad> $localidades
 * @property-read int|null $localidades_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalidadCensal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalidadCensal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalidadCensal onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalidadCensal query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalidadCensal whereCentroideLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalidadCensal whereCentroideLon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalidadCensal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalidadCensal whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalidadCensal whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalidadCensal whereGeorefCategoriaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalidadCensal whereGeorefFuenteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalidadCensal whereGeorefFuncionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalidadCensal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalidadCensal whereIdGeoref($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalidadCensal whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalidadCensal whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalidadCensal whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalidadCensal withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalidadCensal withoutTrashed()
 * @mixin \Eloquent
 */
class LocalidadCensal extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "id_georef",
        "georef_fuente_id",
        "georef_categoria_id",
        "georef_funcion_id",
        "nombre",
        "centroide_lat",
        "centroide_lon"
    ];

    /**
     * Relationship to the georef source.
     */
    public function georefFuente(): BelongsTo
    {
        return $this->belongsTo(GeorefFuente::class);
    }

    /**
     * Relationship to the georef category.
     */
    public function georefCategoria(): BelongsTo
    {
        return $this->belongsTo(GeorefCategoria::class);
    }

    /**
     * Relationship to the georef function.
     */
    public function georefFuncion(): BelongsTo
    {
        return $this->belongsTo(GeorefFuncion::class);
    }

    /**
     * Relationship to the localities.
     */
    public function localidades(): HasMany
    {
        return $this->hasMany(Localidad::class);
    }

    /**
     * Relationship to the georef settlements.
     */
    public function georefAsentamientos(): HasMany
    {
        return $this->hasMany(GeorefAsentamiento::class);
    }

    /**
     * Relationship to the georef localities.
     */
    public function georefLocalidades(): HasMany
    {
        return $this->hasMany(GeorefLocalidad::class);
    }

    /**
     * Relationship to the streets.
     */
    public function calles(): HasMany
    {
        return $this->hasMany(Calle::class);
    }
}
