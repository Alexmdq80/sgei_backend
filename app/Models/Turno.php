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
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Propuesta> $propuestasTurnoFin
 * @property-read int|null $propuestas_turno_fin_count
 * @property-read Collection<int, Propuesta> $propuestasTurnoInicio
 * @property-read int|null $propuestas_turno_inicio_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Turno newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Turno newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Turno onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Turno query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Turno whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Turno whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Turno whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Turno whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Turno whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Turno whereOrden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Turno whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Turno whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Turno withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Turno withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Turno extends Model
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
    ];

    /**
     * Relationship to the proposals starting in this shift.
     */
    public function propuestasTurnoInicio(): HasMany
    {
        return $this->hasMany(Propuesta::class, 'turno_inicio_id', 'id');
    }

    /**
     * Relationship to the proposals ending in this shift.
     */
    public function propuestasTurnoFin(): HasMany
    {
        return $this->hasMany(Propuesta::class, 'turno_fin_id', 'id');
    }
}
