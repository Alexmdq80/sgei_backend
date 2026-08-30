<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string|null $id_georef
 * @property int|null $continente_id
 * @property string $nombre
 * @property string|null $nacionalidad
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Continente|null $continente
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Persona> $nacionalidadPersonas
 * @property-read int|null $nacionalidad_personas_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Persona> $personas
 * @property-read int|null $personas_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Provincia> $provincias
 * @property-read int|null $provincias_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nacion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nacion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nacion onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nacion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nacion whereContinenteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nacion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nacion whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nacion whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nacion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nacion whereIdGeoref($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nacion whereNacionalidad($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nacion whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nacion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nacion whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nacion withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nacion withoutTrashed()
 * @mixin \Eloquent
 */
class Nacion extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "id_georef",
        "continente_id",
        "nombre",
        "nacionalidad"
    ];

    /**
     * Relationship to the continent.
     */
    public function continente(): BelongsTo
    {
        return $this->belongsTo(Continente::class);
    }

    /**
     * Relationship to the provinces in this nation.
     */
    public function provincias(): HasMany
    {
        return $this->hasMany(Provincia::class);
    }

    /**
     * Relationship to the people associated with this nation.
     */
    public function personas(): HasMany
    {
        return $this->hasMany(Persona::class);
    }

    /**
     * Relationship to the people with this nationality.
     */
    public function nacionalidadPersonas(): HasMany
    {
        return $this->hasMany(Persona::class, "nacionalidad_nacion_id", "id");
    }
}
