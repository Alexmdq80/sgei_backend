<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $nivel_id
 * @property int $modalidad_id
 * @property int|null $escuela_tipo_id
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read EscuelaTipo|null $escuelaTipo
 * @property-read EscuelaModalidadNivel|null $pivot
 * @property-read Collection<int, Escuela> $escuelas
 * @property-read int|null $escuelas_count
 * @property-read Modalidad|null $modalidad
 * @property-read Nivel|null $nivel
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModalidadNivel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModalidadNivel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModalidadNivel onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModalidadNivel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModalidadNivel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModalidadNivel whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModalidadNivel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModalidadNivel whereEscuelaTipoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModalidadNivel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModalidadNivel whereModalidadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModalidadNivel whereNivelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModalidadNivel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModalidadNivel whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModalidadNivel withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModalidadNivel withoutTrashed()
 *
 * @mixin \Eloquent
 */
class ModalidadNivel extends Pivot
{
    use AuditableTrait, HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'modalidad_nivel';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nivel_id',
        'modalidad_id',
        'escuela_tipo_id',
    ];

    /**
     * Relationship to the modality.
     */
    public function modalidad(): BelongsTo
    {
        return $this->belongsTo(Modalidad::class);
    }

    /**
     * Relationship to the level.
     */
    public function nivel(): BelongsTo
    {
        return $this->belongsTo(Nivel::class);
    }

    /**
     * Relationship to the school type.
     */
    public function escuelaTipo(): BelongsTo
    {
        return $this->belongsTo(EscuelaTipo::class);
    }

    /**
     * Relationship to the schools.
     */
    public function escuelas(): BelongsToMany
    {
        return $this->belongsToMany(Escuela::class)
            ->using(EscuelaModalidadNivel::class);
    }
}
