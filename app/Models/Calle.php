<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Calle extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "id_georef",
        "nombre",
        "altura_fin_derecha",
        "altura_fin_izquierda",
        "altura_inicio_derecha",
        "altura_inicio_izquierda",
        "localidad_censal_id",
        "georef_fuente_id",
        "georef_categoria_id",
        "created_by",
        "updated_by"
    ];

    /**
     * Relationship to the census locality.
     */
    public function localidadCensal(): BelongsTo
    {
        return $this->belongsTo(LocalidadCensal::class);
    }

    /**
     * Relationship to the georef category.
     */
    public function georefCategoria(): BelongsTo
    {
        return $this->belongsTo(GeorefCategoria::class);
    }

    /**
     * Relationship to the georef source.
     */
    public function georefFuente(): BelongsTo
    {
        return $this->belongsTo(GeorefFuente::class);
    }

    /**
     * Relationship to addresses on this street.
     */
    public function domicilioCalles(): HasMany
    {
        return $this->hasMany(Domicilio::class);
    }

    /**
     * Relationship to addresses between this street (1).
     */
    public function domicilioEntreCalles1(): HasMany
    {
        return $this->hasMany(Domicilio::class, "calle_entre_1_id", "id");
    }

    /**
     * Relationship to addresses between this street (2).
     */
    public function domicilioEntreCalles2(): HasMany
    {
        return $this->hasMany(Domicilio::class, "calle_entre_2_id", "id");
    }
}
