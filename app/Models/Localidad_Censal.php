<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Localidad_Censal extends Model
{
    use HasFactory;

    protected $table = "localidad_censal";

    protected $fillable = ["nombre",
                           "centroide_lat","centroide_lon",
    ];

    public function calles() {
        return $this->hasMany(Calle::class, "id_localidad_censal", "id" );
    }
    public function asentamientos() {
        return $this->hasMany(Asentamiento::class, "id_localidad_censal", "id" );
    }
    public function localidades() {
        return $this->hasMany(Localidad::class, "id_localidad_censal", "id" );
    }
    public function localidades_asentamientos() {
        return $this->hasMany(Localidad_Asentamiento::class, "id_localidad_censal", "id" );
    }   

    public function departamento(){
        return $this->belongsTo(Departamento::class, "id_departamento");
    }
    public function municipio(){
        return $this->belongsTo(Municipio::class, "id_municipio");
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

    public function funcion() {
        return $this->belongsTo(Funcion_Georef::class, "id_funcion_georef");
    }

    public $timestamps = false;
}
