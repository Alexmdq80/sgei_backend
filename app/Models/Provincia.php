<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Provincia extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "id_georef",
        "nacion_id",
        "georef_fuente_id",
        "georef_categoria_id",
        "nombre",
        "nombre_completo",
        "iso_nombre",
        "iso_id",
        "centroide_lat",
        "centroide_lon"
    ];

    /**
     * Relationship to the nation.
     */
    public function nacion(): BelongsTo
    {
        return $this->belongsTo(Nacion::class);
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
     * Relationship to the departments in this province.
     */
    public function departamentos(): HasMany
    {
        return $this->hasMany(Departamento::class);
    }

    /**
     * Relationship to the municipalities in this province.
     */
    public function municipios(): HasMany
    {
        return $this->hasMany(Municipio::class);
    }

    /**
     * Relationship to the people associated with this province.
     */
    public function personas(): HasMany
    {
        return $this->hasMany(Persona::class);
    }
}
