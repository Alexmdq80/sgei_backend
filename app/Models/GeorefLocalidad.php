<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string|null $id_georef
 * @property int|null $departamento_id
 * @property int|null $municipio_id
 * @property int|null $localidad_censal_id
 * @property int|null $georef_fuente_id
 * @property int|null $georef_categoria_id
 * @property string $nombre
 * @property numeric|null $centroide_lat
 * @property numeric|null $centroide_lon
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Departamento|null $departamento
 * @property-read \App\Models\GeorefCategoria|null $georefCategoria
 * @property-read \App\Models\GeorefFuente|null $georefFuente
 * @property-read \App\Models\LocalidadCensal|null $localidadCensal
 * @property-read \App\Models\Municipio|null $municipio
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefLocalidad newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefLocalidad newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefLocalidad onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefLocalidad query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefLocalidad whereCentroideLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefLocalidad whereCentroideLon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefLocalidad whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefLocalidad whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefLocalidad whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefLocalidad whereDepartamentoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefLocalidad whereGeorefCategoriaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefLocalidad whereGeorefFuenteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefLocalidad whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefLocalidad whereIdGeoref($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefLocalidad whereLocalidadCensalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefLocalidad whereMunicipioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefLocalidad whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefLocalidad whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefLocalidad whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefLocalidad withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefLocalidad withoutTrashed()
 * @mixin \Eloquent
 */
class GeorefLocalidad extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "id_georef",
        "departamento_id",
        "municipio_id",
        "localidad_censal_id",
        "georef_fuente_id",
        "georef_categoria_id",
        "nombre",
        "centroide_lat",
        "centroide_lon"
    ];

    /**
     * Relationship to the department.
     */
    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class);
    }

    /**
     * Relationship to the municipality.
     */
    public function municipio(): BelongsTo
    {
        return $this->belongsTo(Municipio::class);
    }

    /**
     * Relationship to the census locality.
     */
    public function localidadCensal(): BelongsTo
    {
        return $this->belongsTo(LocalidadCensal::class);
    }

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
}
