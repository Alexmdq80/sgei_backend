<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $propuesta_id
 * @property int $seccion_tipo_id
 * @property string|null $division
 * @property string|null $division_nombre
 * @property string|null $nombre
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, HistorialInscripcion> $historialInscripciones
 * @property-read int|null $historial_inscripciones_count
 * @property-read Collection<int, Inscripcion> $inscripciones
 * @property-read int|null $inscripciones_count
 * @property-read Propuesta|null $propuesta
 * @property-read SeccionTipo|null $seccionTipo
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Espacio newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Espacio newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Espacio onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Espacio query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Espacio whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Espacio whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Espacio whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Espacio whereDivision($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Espacio whereDivisionNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Espacio whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Espacio whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Espacio wherePropuestaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Espacio whereSeccionTipoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Espacio whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Espacio whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Espacio withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Espacio withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Espacio extends Model
{
    use AuditableTrait, HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'propuesta_id',
        'seccion_tipo_id',
        'division',
        'nombre',
    ];

    /**
     * Relationship to the proposal.
     */
    public function propuesta(): BelongsTo
    {
        return $this->belongsTo(Propuesta::class);
    }

    /**
     * Relationship to the section type.
     */
    public function seccionTipo(): BelongsTo
    {
        return $this->belongsTo(SeccionTipo::class, 'seccion_tipo_id');
    }

    /**
     * Relationship to the registrations.
     */
    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class);
    }

    /**
     * Relationship to the registration history records.
     */
    public function historialInscripciones(): HasMany
    {
        return $this->hasMany(HistorialInscripcion::class);
    }
}
