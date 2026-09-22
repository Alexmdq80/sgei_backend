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
 * @property int|null $orden
 * @property int $vigente
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Cupof> $cupofs
 * @property-read int|null $cupofs_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escalafon newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escalafon newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escalafon onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escalafon query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escalafon whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escalafon whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escalafon whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escalafon whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escalafon whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escalafon whereOrden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escalafon whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escalafon whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escalafon whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escalafon withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escalafon withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Escalafon extends Model
{
    use AuditableTrait, HasFactory, SoftDeletes;

    protected $table = 'escalafones';

    protected $fillable = [
        'nombre',
        'orden',
        'vigente',
    ];

    public function cupofs(): HasMany
    {
        return $this->hasMany(Cupof::class);
    }
}
