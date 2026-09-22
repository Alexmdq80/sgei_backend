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
 * @property-read Collection<int, Plan> $planes
 * @property-read int|null $planes_count
 *
 * @method static \Database\Factories\PlanCicloFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanCiclo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanCiclo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanCiclo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanCiclo query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanCiclo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanCiclo whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanCiclo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanCiclo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanCiclo whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanCiclo whereOrden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanCiclo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanCiclo whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanCiclo whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanCiclo withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanCiclo withoutTrashed()
 *
 * @mixin \Eloquent
 */
class PlanCiclo extends Model
{
    use AuditableTrait, HasFactory, SoftDeletes;

    protected $table = 'plan_ciclos';

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
     * Relationship to the plans in this cycle.
     */
    public function planes(): HasMany
    {
        return $this->hasMany(Plan::class);
    }
}
