<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vinculo extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "vinculo_tipo_id",
        "nombre",
        "orden",
        "vigente"
    ];

    /**
     * Relationship to the person vinculations.
     */
    public function pvps(): HasMany
    {
        return $this->hasMany(PersonaVinculoPersona::class);
    }

    /**
     * Relationship to the link type.
     */
    public function vinculoTipo(): BelongsTo
    {
        return $this->belongsTo(VinculoTipo::class);
    }
}
