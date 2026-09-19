<?php

namespace App\Models;

use App\Traits\ActualizaVersionCatalogo;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
 * @property-read Collection<int, Domicilio> $domicilios
 * @property-read int|null $domicilios_count
 * @property-read GeorefCategoria|null $georefCategoria
 * @property-read GeorefFuente|null $georefFuente
 * @property-read LocalidadCensal|null $localidadCensal
 * @property-read Municipio|null $municipio
 * @property-read Collection<int, Persona> $personasNacidas
 * @property-read int|null $personas_nacidas_count
 *
 * @method static \Database\Factories\LocalidadFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localidad newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localidad newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localidad onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localidad query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localidad whereCentroideLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localidad whereCentroideLon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localidad whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localidad whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localidad whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localidad whereDepartamentoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localidad whereGeorefCategoriaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localidad whereGeorefFuenteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localidad whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localidad whereIdGeoref($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localidad whereLocalidadCensalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localidad whereMunicipioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localidad whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localidad whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localidad whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localidad withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localidad withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Localidad extends Model
{
    use ActualizaVersionCatalogo, AuditableTrait, HasFactory, SoftDeletes;

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
     * Relationship to the people born in this locality.
     */
    public function personasNacidas(): HasMany
    {
        return $this->hasMany(Persona::class);
    }

    /**
     * Relationship to the addresses in this locality.
     */
    public function domicilios(): HasMany
    {
        return $this->hasMany(Domicilio::class);
    }
}
