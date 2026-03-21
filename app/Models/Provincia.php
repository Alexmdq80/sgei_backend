<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provincia extends Model
{

    use HasFactory;

    protected $table = "provincia";

    protected $fillable = ["nombre","nombre_completo","iso_nombre","iso_id",
                           "centroide_lat","centroide_lon"    
    ];

    public function calles() {
        return $this->hasMany(Calle::class, "id_provincia", "id" );
    }
    public function asentamientos() {
        return $this->hasMany(Asentamiento::class, "id_provincia", "id" );
    }
    public function localidades() {
        return $this->hasMany(Localidad::class, "id_provincia", "id" );
    }
    public function localidades_asentamientos() {
        return $this->hasMany(Localidad_Asentamiento::class, "id_provincia", "id" );
    }
    public function localidades_censales() {
        return $this->hasMany(Localidad_Censal::class, "id_provincia", "id" );
    }
    public function departamentos() {
        return $this->hasMany(Departamento::class, "id_provincia", "id" );
    }
    public function municipios() {
        return $this->hasMany(Municipio::class, "id_provincia", "id" );
    }

    public function pais(){
        return $this->belongsTo(Pais::class, "id_pais");
    }
    public function continente(){
        return $this->belongsTo(Continente::class, "id_continente");
    }

    public function categoria_georef() {
        return $this->belongsTo(Categoria_Georef::class, "id_categoria_georef");
    }
    public function fuente() {
        return $this->belongsTo(Fuente::class, "id_fuente_georef");
    }

    public function personas() {
        return $this->hasMany(Persona::class, "nacimiento_lugar_id_provincia", "id" );
    } 
    public function domicilios() {
        return $this->hasMany(Domicilio::class, "id_provincia", "id" );
    }

    public $timestamps = false;
}
