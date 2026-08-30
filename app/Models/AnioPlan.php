<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $plan_id
 * @property int $anio_id
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Anio|null $anio
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Asignatura> $asignaturas
 * @property-read int|null $asignaturas_count
 * @property-read \App\Models\Plan|null $plan
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Propuesta> $propuestas
 * @property-read int|null $propuestas_count
 * @method static \Database\Factories\AnioPlanFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnioPlan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnioPlan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnioPlan onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnioPlan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnioPlan whereAnioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnioPlan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnioPlan whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnioPlan whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnioPlan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnioPlan wherePlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnioPlan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnioPlan whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnioPlan withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnioPlan withoutTrashed()
 * @mixin \Eloquent
 */
class AnioPlan extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $auditGroup = "academic";

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'anio_plan';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "plan_id",
        "anio_id"
    ];

    /**
     * Relationship to the plan.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Relationship to the year.
     */
    public function anio(): BelongsTo
    {
        return $this->belongsTo(Anio::class);
    }

    /**
     * Relationship to the asignaturas in this plan year.
     */
    public function asignaturas(): HasMany
    {
        return $this->hasMany(Asignatura::class);
    }

    /**
     * Relationship to the proposals.
     */
    public function propuestas(): HasMany
    {
        return $this->hasMany(Propuesta::class);
    }
}
