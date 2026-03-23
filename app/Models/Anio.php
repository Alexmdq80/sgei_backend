<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Anio extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "nombre",
        "nombre_completo",
        "anio_absoluto",
        "anio_relativo",
        "orden",
        "vigente",
        "created_by",
        "updated_by"
    ];

    /**
     * Relationship to the plan years.
     */
    public function planAnios(): HasMany
    {
        return $this->hasMany(PlanAnio::class);
    }
}
