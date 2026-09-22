<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $nombre
 * @property int $vigente
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, ModalidadNivel> $modalidadesNiveles
 * @property-read int|null $modalidades_niveles_count
 *
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
 *
 * @mixin \Eloquent
 */
class EscuelaTipo extends Model
{
    use AuditableTrait, HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'vigente',
    ];

    /**
     * Relationship to the modality levels.
     */
    public function modalidadesNiveles(): HasMany
    {
        return $this->hasMany(ModalidadNivel::class);
    }
}
