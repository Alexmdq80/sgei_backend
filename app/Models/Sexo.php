<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $nombre
 * @property string|null $letra
 * @property int|null $orden
 * @property int $vigente
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Persona> $personas
 * @property-read int|null $personas_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sexo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sexo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sexo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sexo query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sexo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sexo whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sexo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sexo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sexo whereLetra($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sexo whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sexo whereOrden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sexo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sexo whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sexo whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sexo withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sexo withoutTrashed()
 * @mixin \Eloquent
 */
class Sexo extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "nombre",
        "letra",
        "orden",
        "vigente"
    ];

    /**
     * Relationship to the people.
     */
    public function personas(): HasMany
    {
        return $this->hasMany(Persona::class);
    }
}
