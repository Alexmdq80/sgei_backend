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
 * @property-read Collection<int, InscripcionPase> $inscripcionPases
 * @property-read int|null $inscripcion_pases_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaUbicacion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaUbicacion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaUbicacion onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaUbicacion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaUbicacion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaUbicacion whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaUbicacion whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaUbicacion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaUbicacion whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaUbicacion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaUbicacion whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaUbicacion whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaUbicacion withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaUbicacion withoutTrashed()
 *
 * @mixin \Eloquent
 */
class EscuelaUbicacion extends Model
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
     * Relationship to the transfer registration records.
     */
    public function inscripcionPases(): HasMany
    {
        return $this->hasMany(InscripcionPase::class);
    }
}
