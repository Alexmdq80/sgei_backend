<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $vinculo_tipo_id
 * @property string $nombre
 * @property int|null $orden
 * @property int $vigente
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PersonaVinculoPersona> $pvps
 * @property-read int|null $pvps_count
 * @property-read \App\Models\VinculoTipo|null $vinculoTipo
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vinculo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vinculo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vinculo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vinculo query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vinculo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vinculo whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vinculo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vinculo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vinculo whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vinculo whereOrden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vinculo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vinculo whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vinculo whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vinculo whereVinculoTipoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vinculo withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vinculo withoutTrashed()
 * @mixin \Eloquent
 */
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
