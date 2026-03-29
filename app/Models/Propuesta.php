<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Propuesta extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "escuela_id",
        "anio_plan_id",
        "turno_inicio_id",
        "turno_fin_id",
        "jornada_id",
        "lectivo_id"
    ];

    /**
     * Relationship to the school.
     */
    public function escuela(): BelongsTo
    {
        return $this->belongsTo(Escuela::class);
    }

    /**
     * Relationship to the academic year plan.
     */
    public function anioPlan(): BelongsTo
    {
        return $this->belongsTo(AnioPlan::class);
    }

    /**
     * Relationship to the shift starting time.
     */
    public function turnoInicio(): BelongsTo
    {
        return $this->belongsTo(Turno::class, "turno_inicio_id", "id");
    }

    /**
     * Relationship to the shift ending time.
     */
    public function turnoFin(): BelongsTo
    {
        return $this->belongsTo(Turno::class, "turno_fin_id", "id");
    }

    /**
     * Relationship to the school journey.
     */
    public function jornada(): BelongsTo
    {
        return $this->belongsTo(Jornada::class);
    }

    /**
     * Relationship to the academic year.
     */
    public function cicloLectivo(): BelongsTo
    {
        return $this->belongsTo(Lectivo::class, 'lectivo_id');
    }

    /**
     * Relationship to the academic spaces.
     */
    public function espacios(): HasMany
    {
        return $this->hasMany(Espacio::class);
    }
}
