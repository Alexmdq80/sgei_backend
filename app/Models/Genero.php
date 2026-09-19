<?php

namespace App\Models;

use App\Traits\ActualizaVersionCatalogo;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $nombre
 * @property int $orden
 * @property int $vigente
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Persona> $personas
 * @property-read int|null $personas_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genero newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genero newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genero onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genero query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genero whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genero whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genero whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genero whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genero whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genero whereOrden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genero whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genero whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genero whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genero withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genero withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Genero extends Model
{
    use ActualizaVersionCatalogo, AuditableTrait, HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'orden',
        'vigente',
    ];

    /**
     * Relationship to the people.
     */
    public function personas(): HasMany
    {
        return $this->hasMany(Persona::class);
    }
}
