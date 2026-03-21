<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Continente extends Model
{
    use HasFactory;

    protected $table = "continente";

    protected $fillable = ["nombre",
                           "orden"
    ];

    public function calles() {
        return $this->hasMany(Calle::class, "id_continente", "id" );
    }
    public function asentamientos() {
        return $this->hasMany(Asentamiento::class, "id_continente", "id" );
    }
    public function localidades() {
        return $this->hasMany(Localidad::class, "id_continente", "id" );
    }
    public function localidades_asentamientos() {
        return $this->hasMany(Localidad_Asentamiento::class, "id_continente", "id" );
    }
    public function localidades_censales() {
        return $this->hasMany(Localidad_Censal::class, "id_continente", "id" );
    }
    public function departamentos() {
        return $this->hasMany(Departamento::class, "id_continente", "id" );
    }
    public function municipios() {
        return $this->hasMany(Municipio::class, "id_continente", "id" );
    }
    public function provincias() {
        return $this->hasMany(Provincia::class, "id_continente", "id" );
    }
    public function paises() {
        return $this->hasMany(Pais::class, "id_continente", "id" );
    }
                            
    public $timestamps = false;
}
