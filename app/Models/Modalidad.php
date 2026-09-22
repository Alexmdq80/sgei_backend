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
 * @property-read Collection<int, Nivel> $niveles
 * @property-read int|null $niveles_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modalidad newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modalidad newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modalidad onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modalidad query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modalidad whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modalidad whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modalidad whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modalidad whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modalidad whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modalidad whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modalidad whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modalidad whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modalidad withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modalidad withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Modalidad extends Model
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
     * Relationship to the levels associated with this modality.
     */
    public function niveles(): BelongsToMany
    {
        return $this->belongsToMany(Nivel::class)
            ->using(ModalidadNivel::class);
    }

    /**
     * Relationship to the registrations as school of origin modality.
     */
    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class);
    }

    /**
     * Relationship to the registration history as school of origin modality.
     */
    public function historialInscripciones(): HasMany
    {
        return $this->hasMany(HistorialInscripcion::class);
    }
}
