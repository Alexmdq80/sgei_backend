<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $vinculo_tipo_id
 * @property string $nombre
 * @property int|null $orden
 * @property int $vigente
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, PersonaVinculoPersona> $pvps
 * @property-read int|null $pvps_count
 * @property-read VinculoTipo|null $vinculoTipo
 *
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
 *
 * @mixin \Eloquent
 */
class Vinculo extends Model
{
    use AuditableTrait, HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vinculo_tipo_id',
        'nombre',
        'orden',
        'vigente',
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
