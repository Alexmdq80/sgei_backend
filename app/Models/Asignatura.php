<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $nombre
 * @property string|null $nombre_completo
 * @property int $anio_plan_id
 * @property int $horas_semanales
 * @property string|null $codigo
 * @property int $orden
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AnioPlan|null $anioPlan
 * @method static \Database\Factories\AsignaturaFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignatura newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignatura newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignatura onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignatura query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignatura whereAnioPlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignatura whereCodigo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignatura whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignatura whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignatura whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignatura whereHorasSemanales($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignatura whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignatura whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignatura whereNombreCompleto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignatura whereOrden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignatura whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignatura whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignatura withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignatura withoutTrashed()
 * @mixin \Eloquent
 */
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
