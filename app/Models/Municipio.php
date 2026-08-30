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
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\GeorefCategoria|null $georefCategoria
 * @property-read \App\Models\GeorefFuente|null $georefFuente
 * @property-read \App\Models\Provincia|null $provincia
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereCentroideLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereCentroideLon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereGeorefCategoriaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereGeorefFuenteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereIdGeoref($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereNombreCompleto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereProvinciaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereProvinciaInterseccion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio withoutTrashed()
 * @mixin \Eloquent
 */
class Municipio extends Model
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
        "georef_fuente_id",
        "georef_categoria_id",
        "nombre",
        "nombre_completo",
        "centroide_lat",
        "centroide_lon",
        "provincia_interseccion"
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
     * Relationship to the province.
     */
    public function provincia(): BelongsTo
    {
        return $this->belongsTo(Provincia::class);
    }
}
