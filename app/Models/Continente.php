<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $nombre
 * @property int $vigente
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Nacion> $naciones
 * @property-read int|null $naciones_count
 *
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
 *
 * @mixin \Eloquent
 */
class Continente extends Model
{
    use AuditableTrait, HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'vigente',
    ];

    /**
     * Relationship to the nations in this continent.
     */
    public function naciones(): HasMany
    {
        return $this->hasMany(Nacion::class);
    }
}
