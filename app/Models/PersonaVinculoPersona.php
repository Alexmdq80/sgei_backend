<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonaVinculoPersona extends Pivot
{
    use HasFactory, SoftDeletes, AuditableTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "persona_vinculo_persona";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "persona_estudiante_id",
        "persona_adulto_id",
        "vinculo_id",
        "detalle",
        "vencimiento_fecha"
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'vencimiento_fecha' => 'datetime'
    ];

    /**
     * Relationship to the link definition.
     */
    public function vinculo(): BelongsTo
    {
        return $this->belongsTo(Vinculo::class);
    }

    /**
     * Relationship to the student.
     */
    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Persona::class, "persona_estudiante_id");
    }

    /**
     * Relationship to the adult.
     */
    public function adulto(): BelongsTo
    {
        return $this->belongsTo(Persona::class, "persona_adulto_id");
    }
}
