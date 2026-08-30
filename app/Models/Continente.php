<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $nombre
 * @property int $vigente
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Nacion> $naciones
 * @property-read int|null $naciones_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Continente newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Continente newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Continente onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Continente query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Continente whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Continente whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Continente whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Continente whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Continente whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Continente whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Continente whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Continente whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Continente withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Continente withoutTrashed()
 * @mixin \Eloquent
 */
class Continente extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "nombre",
        "vigente"
    ];

    /**
     * Relationship to the nations in this continent.
     */
    public function naciones(): HasMany
    {
        return $this->hasMany(Nacion::class);
    }
}
