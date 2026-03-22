<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Turno extends Model
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
     * Relationship to the proposals starting in this shift.
     */
    public function propuestasTurnoInicio(): HasMany
    {
        return $this->hasMany(Propuesta::class, "turno_inicio_id", "id");
    }

    /**
     * Relationship to the proposals ending in this shift.
     */
    public function propuestasTurnoFin(): HasMany
    {
        return $this->hasMany(Propuesta::class, "turno_fin_id", "id");
    }
}
