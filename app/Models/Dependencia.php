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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dependencia newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dependencia newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dependencia onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dependencia query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dependencia whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dependencia whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dependencia whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dependencia whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dependencia whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dependencia whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dependencia whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dependencia whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dependencia withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dependencia withoutTrashed()
 * @mixin \Eloquent
 */
class Dependencia extends Model
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
     * Relationship to the schools in this dependency.
     */
    public function escuelas(): HasMany
    {
        return $this->hasMany(Escuela::class);
    }
}
