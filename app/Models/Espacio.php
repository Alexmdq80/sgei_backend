<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $propuesta_id
 * @property int $seccion_tipo_id
 * @property string|null $division
 * @property string|null $division_nombre
 * @property string|null $nombre
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HistorialInscripcion> $historialInscripciones
 * @property-read int|null $historial_inscripciones_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Inscripcion> $inscripciones
 * @property-read int|null $inscripciones_count
 * @property-read \App\Models\Propuesta|null $propuesta
 * @property-read \App\Models\SeccionTipo|null $seccionTipo
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
 * @mixin \Eloquent
 */
class Espacio extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "propuesta_id",
        "seccion_tipo_id",
        "division",
        "nombre"
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
        return $this->belongsTo(SeccionTipo::class, "seccion_tipo_id");
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
