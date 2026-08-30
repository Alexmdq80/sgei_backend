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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LocalidadCensal> $localidadesCensales
 * @property-read int|null $localidades_censales_count
 * @method static \Database\Factories\GeorefFuncionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefFuncion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefFuncion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefFuncion onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefFuncion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefFuncion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefFuncion whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefFuncion whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefFuncion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefFuncion whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefFuncion whereOrden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefFuncion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefFuncion whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefFuncion whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefFuncion withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefFuncion withoutTrashed()
 * @mixin \Eloquent
 */
class GeorefFuncion extends Model
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
     * Relationship to the census localities.
     */
    public function localidadesCensales(): HasMany
    {
        return $this->hasMany(LocalidadCensal::class);
    }
}
