<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asignatura extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $auditGroup = "academic";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "nombre",
        "nombre_completo",
        "anio_plan_id",
        "horas_semanales",
        "codigo",
        "orden"
    ];

    /**
     * Relationship to the specific plan year.
     */
    public function anioPlan(): BelongsTo
    {
        return $this->belongsTo(AnioPlan::class);
    }
}
