<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $nombre
 * @property string|null $nombre_completo
 * @property int|null $anio_absoluto
 * @property int|null $anio_relativo
 * @property int $vigente
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AnioPlan> $planAnios
 * @property-read int|null $plan_anios_count
 * @method static \Database\Factories\AnioFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anio newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anio newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anio onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anio query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anio whereAnioAbsoluto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anio whereAnioRelativo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anio whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anio whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anio whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anio whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anio whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anio whereNombreCompleto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anio whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anio whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anio whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anio withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anio withoutTrashed()
 * @mixin \Eloquent
 */
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
        "vigente",
        "created_by",
        "updated_by"
    ];

    /**
     * Relationship to the plan years.
     */
    public function planAnios(): HasMany
    {
        return $this->hasMany(AnioPlan::class);
    }
}
