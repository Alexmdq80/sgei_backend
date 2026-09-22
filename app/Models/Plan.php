<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $plan_ciclo_id
 * @property string $nombre
 * @property string|null $nombre_completo
 * @property int|null $duracion_anios
 * @property string|null $resolucion
 * @property string|null $orientacion
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, AnioPlan> $anioPlanes
 * @property-read int|null $anio_planes_count
 * @property-read PlanCiclo|null $planCiclo
 *
 * @method static \Database\Factories\PlanFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereDuracionAnios($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereNombreCompleto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereOrientacion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan wherePlanCicloId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereResolucion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Plan extends Model
{
    use AuditableTrait, HasFactory, SoftDeletes;

    protected $table = 'plans';

    protected $auditGroup = 'academic';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'plan_ciclo_id',
        'nombre',
        'nombre_completo',
        'duracion_anios',
        'resolucion',
        'orientacion',
    ];

    /**
     * Relationship to the years in this plan.
     */
    public function anioPlanes(): HasMany
    {
        return $this->hasMany(AnioPlan::class);
    }

    /**
     * Relationship to the cycle this plan belongs to.
     */
    public function planCiclo(): BelongsTo
    {
        return $this->belongsTo(PlanCiclo::class);
    }
}
