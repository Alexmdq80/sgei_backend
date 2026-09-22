<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
 * @property-read Collection<int, HistorialInscripcion> $historialInscripciones
 * @property-read int|null $historial_inscripciones_count
 * @property-read Collection<int, Inscripcion> $inscripciones
 * @property-read int|null $inscripciones_count
 * @property-read ModalidadNivel|null $pivot
 * @property-read Collection<int, Modalidad> $modalidades
 * @property-read int|null $modalidades_count
 *
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
 *
 * @mixin \Eloquent
 */
class Nivel extends Model
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
