<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GeorefCategoria extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "nombre",
        "orden",
        "vigente"
    ];

    /**
     * Relationship to the streets.
     */
    public function calles(): HasMany
    {
        return $this->hasMany(Calle::class);
    }

    /**
     * Relationship to the settlements.
     */
    public function asentamientos(): HasMany
    {
        return $this->hasMany(GeorefAsentamiento::class);
    }

    /**
     * Relationship to the localities.
     */
    public function localidades(): HasMany
    {
        return $this->hasMany(Localidad::class);
    }

    /**
     * Relationship to the census localities.
     */
    public function localidadesCensales(): HasMany
    {
        return $this->hasMany(LocalidadCensal::class);
    }

    /**
     * Relationship to the municipalities.
     */
    public function municipios(): HasMany
    {
        return $this->hasMany(Municipio::class);
    }

    /**
     * Relationship to the departments.
     */
    public function departamentos(): HasMany
    {
        return $this->hasMany(Departamento::class);
    }

    /**
     * Relationship to the provinces.
     */
    public function provincias(): HasMany
    {
        return $this->hasMany(Provincia::class);
    }
}
