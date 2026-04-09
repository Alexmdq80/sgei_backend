<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
