<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $nombre
 * @property int|null $orden
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Espacio> $espacios
 * @property-read int|null $espacios_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeccionTipo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeccionTipo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeccionTipo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeccionTipo query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeccionTipo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeccionTipo whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeccionTipo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeccionTipo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeccionTipo whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeccionTipo whereOrden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeccionTipo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeccionTipo whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeccionTipo withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeccionTipo withoutTrashed()
 * @mixin \Eloquent
 */
class SeccionTipo extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "nombre",
        "orden"
    ];

    /**
     * Relationship to the academic spaces.
     */
    public function espacios(): HasMany
    {
        return $this->hasMany(Espacio::class);
    }
}
