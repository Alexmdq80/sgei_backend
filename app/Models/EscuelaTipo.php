<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $nombre
 * @property int $vigente
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ModalidadNivel> $modalidadesNiveles
 * @property-read int|null $modalidades_niveles_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaTipo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaTipo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaTipo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaTipo query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaTipo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaTipo whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaTipo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaTipo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaTipo whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaTipo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaTipo whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaTipo whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaTipo withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaTipo withoutTrashed()
 * @mixin \Eloquent
 */
class EscuelaTipo extends Model
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
     * Relationship to the modality levels.
     */
    public function modalidadesNiveles(): HasMany
    {
        return $this->hasMany(ModalidadNivel::class);
    }
}
