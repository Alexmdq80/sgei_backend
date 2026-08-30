<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InscripcionFinalizado> $inscripcionFinalizados
 * @property-read int|null $inscripcion_finalizados_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Inscripcion> $inscripciones
 * @property-read int|null $inscripciones_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condicion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condicion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condicion onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condicion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condicion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condicion whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condicion whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condicion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condicion whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condicion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condicion whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condicion whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condicion withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condicion withoutTrashed()
 * @mixin \Eloquent
 */
class Condicion extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "nombre",
        "vigente",
        "created_by",
        "updated_by"
    ];

    /**
     * Relationship to the registrations.
     */
    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class);
    }

    /**
     * Relationship to the finalized registrations.
     */
    public function inscripcionFinalizados(): HasMany
    {
        return $this->hasMany(InscripcionFinalizado::class);
    }

    /**
     * Relationship to the registration history records.
     */
    public function historialInscripciones(): HasMany
    {
        return $this->hasMany(HistorialInscripcion::class);
    }
}
