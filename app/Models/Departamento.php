<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    use HasFactory;

    protected $table = "departamento";

    protected $fillable = ["nombre","nombre_completo",
                           "centroide_lat","centroide_lon",
                           "provincia_interseccion"
    ];   

    public function calles() {
        return $this->hasMany(Calle::class, "id_departamento", "id" );
    }
    public function asentamientos() {
        return $this->hasMany(Asentamiento::class, "id_departamento", "id" );
    }
    public function localidades() {
        return $this->hasMany(Localidad::class, "id_departamento", "id" );
    }
    public function localidades_asentamientos() {
        return $this->hasMany(Localidad_Asentamiento::class, "id_departamento", "id" );
    }
    public function localidades_censales() {
        return $this->hasMany(Localidad_Censal::class, "id_departamento", "id" );
    }
    
    public function provincia(){
        return $this->belongsTo(Provincia::class, "id_provincia");
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
        return $this->hasMany(Persona::class, "nacimiento_lugar_id_departamento", "id" );
    }   

    public function domicilios() {
        return $this->hasMany(Domicilio::class, "id_departamento", "id" );
    }

    public $timestamps = false;
}
