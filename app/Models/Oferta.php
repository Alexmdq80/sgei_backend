<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $nombre
 * @property int $vigente
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Escuela> $escuelas
 * @property-read int|null $escuelas_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Oferta newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Oferta newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Oferta onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Oferta query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Oferta whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Oferta whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Oferta whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Oferta whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Oferta whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Oferta whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Oferta whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Oferta whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Oferta withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Oferta withoutTrashed()
 * @mixin \Eloquent
 */
class Oferta extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "nombre",
        "vigente"
    ];

    /**
     * Relationship to the schools offering this.
     */
    public function escuelas(): BelongsToMany
    {
        return $this->belongsToMany(Escuela::class);
    }
}
