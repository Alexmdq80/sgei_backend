<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlanAnio extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

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
     * Relationship to the proposals.
     */
    public function propuestas(): HasMany
    {
        return $this->hasMany(Propuesta::class);
    }
}
