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
 * @property-read Collection<int, Vinculo> $vinculos
 * @property-read int|null $vinculos_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VinculoTipo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VinculoTipo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VinculoTipo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VinculoTipo query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VinculoTipo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VinculoTipo whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VinculoTipo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VinculoTipo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VinculoTipo whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VinculoTipo whereOrden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VinculoTipo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VinculoTipo whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VinculoTipo whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VinculoTipo withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VinculoTipo withoutTrashed()
 *
 * @mixin \Eloquent
 */
class VinculoTipo extends Model
{
    use AuditableTrait, HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'orden',
        'vigente',
    ];

    /**
     * Relationship to the links of this type.
     */
    public function vinculos(): HasMany
    {
        return $this->hasMany(Vinculo::class);
    }
}
