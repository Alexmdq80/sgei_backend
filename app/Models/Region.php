<?php

namespace App\Models;

use App\Traits\ActualizaVersionCatalogo;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $provincia_id
 * @property string $numero
 * @property int $vigente
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Departamento> $departamentos
 * @property-read int|null $departamentos_count
 * @property-read Provincia|null $provincia
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereNumero($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereProvinciaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Region extends Model
{
    use ActualizaVersionCatalogo, AuditableTrait, HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'provincia_id',
        'numero',
        'vigente',
    ];

    /**
     * Relationship to the province.
     */
    public function provincia(): BelongsTo
    {
        return $this->belongsTo(Provincia::class);
    }

    /**
     * Relationship to the departments in this region.
     */
    public function departamentos(): HasMany
    {
        return $this->hasMany(Departamento::class);
    }
}
