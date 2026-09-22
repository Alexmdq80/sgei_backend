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
 * @property-read Collection<int, Escuela> $escuelas
 * @property-read int|null $escuelas_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ambito newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ambito newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ambito onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ambito query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ambito whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ambito whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ambito whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ambito whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ambito whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ambito whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ambito whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ambito whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ambito withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ambito withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Ambito extends Model
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
        'created_by',
        'updated_by',
    ];

    /**
     * Relationship to the schools in this ambit.
     */
    public function escuelas(): HasMany
    {
        return $this->hasMany(Escuela::class);
    }
}
