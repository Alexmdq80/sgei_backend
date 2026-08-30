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
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Propuesta> $propuestas
 * @property-read int|null $propuestas_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jornada newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jornada newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jornada onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jornada query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jornada whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jornada whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jornada whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jornada whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jornada whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jornada whereOrden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jornada whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jornada whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jornada withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jornada withoutTrashed()
 * @mixin \Eloquent
 */
class Jornada extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "nombre",
        "orden"
    ];

    /**
     * Relationship to the institutional proposals.
     */
    public function propuestas(): HasMany
    {
        return $this->hasMany(Propuesta::class);
    }
}
