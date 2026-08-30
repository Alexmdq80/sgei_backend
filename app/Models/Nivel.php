<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HistorialInscripcion> $historialInscripciones
 * @property-read int|null $historial_inscripciones_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Inscripcion> $inscripciones
 * @property-read int|null $inscripciones_count
 * @property-read \App\Models\ModalidadNivel|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modalidad> $modalidades
 * @property-read int|null $modalidades_count
 * @method static \Database\Factories\NivelFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nivel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nivel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nivel onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nivel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nivel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nivel whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nivel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nivel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nivel whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nivel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nivel whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nivel whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nivel withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nivel withoutTrashed()
 * @mixin \Eloquent
 */
class Nivel extends Model
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
     * Relationship to the modalities associated with this level.
     */
    public function modalidades(): BelongsToMany
    {
        return $this->belongsToMany(Modalidad::class)
                    ->using(ModalidadNivel::class);
    }

    /**
     * Relationship to the registrations as school of origin level.
     */
    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class);
    }

    /**
     * Relationship to the registration history as school of origin level.
     */
    public function historialInscripciones(): HasMany
    {
        return $this->hasMany(HistorialInscripcion::class);
    }
}
