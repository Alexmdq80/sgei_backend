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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Escuela> $escuelas
 * @property-read int|null $escuelas_count
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
 * @mixin \Eloquent
 */
class Ambito extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "nombre",
        "vigente",
        "created_by",
        "updated_by"
    ];

    /**
     * Relationship to the schools in this ambit.
     */
    public function escuelas(): HasMany
    {
        return $this->hasMany(Escuela::class);
    }
}
