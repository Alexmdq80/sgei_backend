<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pais extends Model
{
    use HasFactory;

    protected $table = "pais";

    protected $fillable = ["nombre",
                           "nacionalidad"
    ]; 

    public function calles() {
        return $this->hasMany(Calle::class, "id_pais", "id" );
    }
    public function asentamientos() {
        return $this->hasMany(Asentamiento::class, "id_pais", "id" );
    }
    public function localidades() {
        return $this->hasMany(Localidad::class, "id_pais", "id" );
    }
    public function localidades_asentamientos() {
        return $this->hasMany(Localidad_Asentamiento::class, "id_pais", "id" );
    }
    public function localidades_censales() {
        return $this->hasMany(Localidad_Censal::class, "id_pais", "id" );
    }
    public function departamentos() {
        return $this->hasMany(Departamento::class, "id_pais", "id" );
    }
    public function municipios() {
        return $this->hasMany(Municipio::class, "id_pais", "id" );
    }
    public function provincias() {
        return $this->hasMany(Provincia::class, "id_pais", "id" );
    }

    public function continente(){
        return $this->belongsTo(Continente::class, "id_continente");
    }

    public function personas() {
        return $this->hasMany(Persona::class, "nacimiento_lugar_id_pais", "id" );
    }
    public function nacionalidad_personas() {
        return $this->hasMany(Persona::class, "nacionalidad_id_pais", "id" );
    }  
    public function domicilios() {
        return $this->hasMany(Domicilio::class, "id_pais", "id" );
    }

    public $timestamps = false;

}
