<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LocalidadCensal extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "id_georef",
        "georef_fuente_id",
        "georef_categoria_id",
        "georef_funcion_id",
        "nombre",
        "centroide_lat",
        "centroide_lon"
    ];

    /**
     * Relationship to the georef source.
     */
    public function georefFuente(): BelongsTo
    {
        return $this->belongsTo(GeorefFuente::class);
    }

    /**
     * Relationship to the georef category.
     */
    public function georefCategoria(): BelongsTo
    {
        return $this->belongsTo(GeorefCategoria::class);
    }

    /**
     * Relationship to the georef function.
     */
    public function georefFuncion(): BelongsTo
    {
        return $this->belongsTo(GeorefFuncion::class);
    }

    /**
     * Relationship to the localities.
     */
    public function localidades(): HasMany
    {
        return $this->hasMany(Localidad::class);
    }

    /**
     * Relationship to the georef settlements.
     */
    public function georefAsentamientos(): HasMany
    {
        return $this->hasMany(GeorefAsentamiento::class);
    }

    /**
     * Relationship to the georef localities.
     */
    public function georefLocalidades(): HasMany
    {
        return $this->hasMany(GeorefLocalidad::class);
    }

    /**
     * Relationship to the streets.
     */
    public function calles(): HasMany
    {
        return $this->hasMany(Calle::class);
    }
}
