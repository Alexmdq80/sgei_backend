<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $nombre
 * @property int|null $anio
 * @property int|null $orden
 * @property int $cerrado
 * @property int $vigente
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Propuesta> $propuestas
 * @property-read int|null $propuestas_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lectivo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lectivo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lectivo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lectivo query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lectivo whereAnio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lectivo whereCerrado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lectivo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lectivo whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lectivo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lectivo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lectivo whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lectivo whereOrden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lectivo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lectivo whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lectivo whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lectivo withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lectivo withoutTrashed()
 * @mixin \Eloquent
 */
class Lectivo extends Model
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
        "anio",
        "orden",
        "vigente",
        "cerrado",
        "created_by",
        "updated_by"
    ];

    /**
     * Relationship to the proposals in this academic year.
     */
    public function propuestas(): HasMany
    {
        return $this->hasMany(Propuesta::class);
    }
}
