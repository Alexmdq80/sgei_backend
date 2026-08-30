<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $persona_id
 * @property string|null $legajo
 * @property \Illuminate\Support\Carbon|null $fecha_ingreso_sistema
 * @property string $estado_administrativo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Persona|null $persona
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agente newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agente newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agente onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agente query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agente whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agente whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agente whereEstadoAdministrativo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agente whereFechaIngresoSistema($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agente whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agente whereLegajo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agente wherePersonaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agente whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agente withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agente withoutTrashed()
 * @mixin \Eloquent
 */
class Agente extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $auditGroup = 'entities';

    protected $fillable = [
        'persona_id',
        'legajo',
        'fecha_ingreso_sistema',
        'estado_administrativo'
    ];

    protected $casts = [
        'fecha_ingreso_sistema' => 'date'
    ];

    /**
     * Relationship to the persona.
     */
    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }
}
