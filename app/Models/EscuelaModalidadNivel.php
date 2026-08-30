<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property int $escuela_id
 * @property int $modalidad_nivel_id
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Escuela|null $escuela
 * @property-read \App\Models\ModalidadNivel|null $modalidadNivel
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaModalidadNivel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaModalidadNivel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaModalidadNivel onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaModalidadNivel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaModalidadNivel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaModalidadNivel whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaModalidadNivel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaModalidadNivel whereEscuelaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaModalidadNivel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaModalidadNivel whereModalidadNivelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaModalidadNivel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaModalidadNivel whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaModalidadNivel withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaModalidadNivel withoutTrashed()
 * @mixin \Eloquent
 */
class EscuelaModalidadNivel extends Pivot
{
    use HasFactory, SoftDeletes, AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "escuela_id",
        "modalidad_nivel_id"
    ];

    /**
     * Relationship to the school.
     */
    public function escuela(): BelongsTo
    {
        return $this->belongsTo(Escuela::class);
    }

    /**
     * Relationship to the modality level.
     */
    public function modalidadNivel(): BelongsTo
    {
        return $this->belongsTo(ModalidadNivel::class);
    }
}
