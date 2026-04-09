<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlanCiclo extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'plan_ciclos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "nombre",
        "orden",
        "vigente"
    ];

    /**
     * Relationship to the plans in this cycle.
     */
    public function planes(): HasMany
    {
        return $this->hasMany(Plan::class);
    }
}
