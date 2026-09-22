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
 * @property int|null $orden
 * @property int $vigente
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Cupof> $cupofs
 * @property-read int|null $cupofs_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PuestoTipo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PuestoTipo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PuestoTipo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PuestoTipo query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PuestoTipo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PuestoTipo whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PuestoTipo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PuestoTipo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PuestoTipo whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PuestoTipo whereOrden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PuestoTipo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PuestoTipo whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PuestoTipo whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PuestoTipo withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PuestoTipo withoutTrashed()
 *
 * @mixin \Eloquent
 */
class PuestoTipo extends Model
{
    use AuditableTrait, HasFactory, SoftDeletes;

    protected $table = 'puesto_tipos';

    protected $fillable = [
        'nombre',
        'orden',
        'vigente',
    ];

    public function cupofs(): HasMany
    {
        return $this->hasMany(Cupof::class);
    }
}
