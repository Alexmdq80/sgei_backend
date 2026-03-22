<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "plan_ciclo_id",
        "nombre",
        "nombre_completo",
        "duracion_anios",
        "resolucion",
        "orientacion"
    ];

    /**
     * Relationship to the years in this plan.
     */
    public function planAnios(): HasMany
    {
        return $this->hasMany(PlanAnio::class);
    }

    /**
     * Relationship to the cycle this plan belongs to.
     */
    public function planCiclo(): BelongsTo
    {
        return $this->belongsTo(PlanCiclo::class);
    }
}
