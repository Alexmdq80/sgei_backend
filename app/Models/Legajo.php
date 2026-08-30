<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $persona_id
 * @property int $escuela_id
 * @property string|null $libro
 * @property string|null $folio
 * @property string|null $legajo
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Escuela|null $escuela
 * @property-read \App\Models\Persona|null $persona
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legajo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legajo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legajo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legajo query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legajo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legajo whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legajo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legajo whereEscuelaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legajo whereFolio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legajo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legajo whereLegajo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legajo whereLibro($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legajo wherePersonaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legajo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legajo whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legajo withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legajo withoutTrashed()
 * @mixin \Eloquent
 */
class Legajo extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $auditGroup = "entities";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "persona_id",
        "escuela_id",
        "libro",
        "folio",
        "legajo"
    ];

    /**
     * Relationship to the person (student).
     */
    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }

    /**
     * Relationship to the school.
     */
    public function escuela(): BelongsTo
    {
        return $this->belongsTo(Escuela::class);
    }
}
