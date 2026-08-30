<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $persona_estudiante_id
 * @property int $persona_adulto_id
 * @property int $vinculo_id
 * @property string|null $detalle
 * @property \Illuminate\Support\Carbon|null $vencimiento_fecha
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Persona|null $adulto
 * @property-read \App\Models\Persona|null $estudiante
 * @property-read \App\Models\Vinculo|null $vinculo
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonaVinculoPersona newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonaVinculoPersona newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonaVinculoPersona onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonaVinculoPersona query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonaVinculoPersona whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonaVinculoPersona whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonaVinculoPersona whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonaVinculoPersona whereDetalle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonaVinculoPersona whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonaVinculoPersona wherePersonaAdultoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonaVinculoPersona wherePersonaEstudianteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonaVinculoPersona whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonaVinculoPersona whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonaVinculoPersona whereVencimientoFecha($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonaVinculoPersona whereVinculoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonaVinculoPersona withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonaVinculoPersona withoutTrashed()
 * @mixin \Eloquent
 */
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
