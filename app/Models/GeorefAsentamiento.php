<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

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
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Departamento|null $departamento
 * @property-read GeorefCategoria|null $georefCategoria
 * @property-read GeorefFuente|null $georefFuente
 * @property-read LocalidadCensal|null $localidadCensal
 * @property-read Municipio|null $municipio
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefAsentamiento newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefAsentamiento newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefAsentamiento onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefAsentamiento query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefAsentamiento whereCentroideLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefAsentamiento whereCentroideLon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefAsentamiento whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefAsentamiento whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefAsentamiento whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefAsentamiento whereDepartamentoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefAsentamiento whereGeorefCategoriaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefAsentamiento whereGeorefFuenteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefAsentamiento whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefAsentamiento whereIdGeoref($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefAsentamiento whereLocalidadCensalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefAsentamiento whereMunicipioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefAsentamiento whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefAsentamiento whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefAsentamiento whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefAsentamiento withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefAsentamiento withoutTrashed()
 *
 * @mixin \Eloquent
 */
class GeorefAsentamiento extends Model
{
    use AuditableTrait, HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_georef',
        'departamento_id',
        'municipio_id',
        'localidad_censal_id',
        'georef_fuente_id',
        'georef_categoria_id',
        'nombre',
        'centroide_lat',
        'centroide_lon',
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
}
