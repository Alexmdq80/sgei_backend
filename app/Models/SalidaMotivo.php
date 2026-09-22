<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $nombre
 * @property int|null $orden
 * @property int $vigente
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, InscripcionBaja> $inscripcionBajas
 * @property-read int|null $inscripcion_bajas_count
 * @property-read Collection<int, InscripcionPase> $inscripcionPases
 * @property-read int|null $inscripcion_pases_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalidaMotivo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalidaMotivo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalidaMotivo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalidaMotivo query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalidaMotivo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalidaMotivo whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalidaMotivo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalidaMotivo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalidaMotivo whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalidaMotivo whereOrden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalidaMotivo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalidaMotivo whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalidaMotivo whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalidaMotivo withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalidaMotivo withoutTrashed()
 *
 * @mixin \Eloquent
 */
class SalidaMotivo extends Model
{
    use AuditableTrait, HasFactory, SoftDeletes;

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
     * Relationship to the registration cancel records.
     */
    public function inscripcionBajas(): HasMany
    {
        return $this->hasMany(InscripcionBaja::class);
    }

    /**
     * Relationship to the registration transfer records.
     */
    public function inscripcionPases(): HasMany
    {
        return $this->hasMany(InscripcionPase::class);
    }
}
