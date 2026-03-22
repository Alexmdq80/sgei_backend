<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ModalidadNivel extends Pivot
{
    use HasFactory, SoftDeletes, AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "nivel_id",
        "modalidad_id",
        "escuela_tipo_id"
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
