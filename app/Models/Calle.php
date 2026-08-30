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
 * @property string $nombre
 * @property int|null $altura_fin_derecha
 * @property int|null $altura_fin_izquierda
 * @property int|null $altura_inicio_derecha
 * @property int|null $altura_inicio_izquierda
 * @property int|null $localidad_censal_id
 * @property int|null $georef_fuente_id
 * @property int|null $georef_categoria_id
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Domicilio> $domicilioCalles
 * @property-read int|null $domicilio_calles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Domicilio> $domicilioEntreCalles1
 * @property-read int|null $domicilio_entre_calles1_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Domicilio> $domicilioEntreCalles2
 * @property-read int|null $domicilio_entre_calles2_count
 * @property-read \App\Models\GeorefCategoria|null $georefCategoria
 * @property-read \App\Models\GeorefFuente|null $georefFuente
 * @property-read \App\Models\LocalidadCensal|null $localidadCensal
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calle onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calle query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calle whereAlturaFinDerecha($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calle whereAlturaFinIzquierda($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calle whereAlturaInicioDerecha($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calle whereAlturaInicioIzquierda($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calle whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calle whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calle whereGeorefCategoriaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calle whereGeorefFuenteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calle whereIdGeoref($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calle whereLocalidadCensalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calle whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calle whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calle whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calle withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calle withoutTrashed()
 * @mixin \Eloquent
 */
class Calle extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "id_georef",
        "nombre",
        "altura_fin_derecha",
        "altura_fin_izquierda",
        "altura_inicio_derecha",
        "altura_inicio_izquierda",
        "localidad_censal_id",
        "georef_fuente_id",
        "georef_categoria_id",
        "created_by",
        "updated_by"
    ];

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

    /**
     * Relationship to addresses on this street.
     */
    public function domicilioCalles(): HasMany
    {
        return $this->hasMany(Domicilio::class);
    }

    /**
     * Relationship to addresses between this street (1).
     */
    public function domicilioEntreCalles1(): HasMany
    {
        return $this->hasMany(Domicilio::class, "calle_entre_1_id", "id");
    }

    /**
     * Relationship to addresses between this street (2).
     */
    public function domicilioEntreCalles2(): HasMany
    {
        return $this->hasMany(Domicilio::class, "calle_entre_2_id", "id");
    }
}
