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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HistorialInfoInscripcion> $historialInfoInscripciones
 * @property-read int|null $historial_info_inscripciones_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CierreCausa newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CierreCausa newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CierreCausa onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CierreCausa query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CierreCausa whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CierreCausa whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CierreCausa whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CierreCausa whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CierreCausa whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CierreCausa whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CierreCausa whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CierreCausa whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CierreCausa withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CierreCausa withoutTrashed()
 * @mixin \Eloquent
 */
class CierreCausa extends Model
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
     * Relationship to the history extra info records.
     */
    public function historialInfoInscripciones(): HasMany
    {
        return $this->hasMany(HistorialInfoInscripcion::class);
    }
}
