<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Escuela extends Model
{
    use HasFactory;

    protected $table = "escuela";

    protected $fillable = ["cue_anexo","clave_provincial",
                           "nombre","numero","codigo_localidad",
                           "domicilio","telefono","email","codigo_postal" 
                        ];                        
    public $timestamps = false;

    public function localidad_asentamiento() {
        return $this->belongsTo(Localidad_Asentamiento::class, "id_localidad_asentamiento");
    }
    public function departamento() {
        return $this->belongsTo(Departamento::class, "id_departamento");
    }
    public function provincia() {
        return $this->belongsTo(Provincia::class, "id_provincia");
    }
    public function pais() {
        return $this->belongsTo(Pais::class, "id_pais");
    }
    public function continente() {
        return $this->belongsTo(Continente::class, "id_continente");
    }
    public function ambito() {
        return $this->belongsTo(Ambito::class, "id_ambito");
    }
    public function dependencia() {
        return $this->belongsTo(Dependencia::class, "id_dependencia");
    }
    public function sector() {
        return $this->belongsTo(Sector::class, "id_sector");
    }
    public function niveles() {
        return $this->belongsToMany(Nivel::class,"escuela_nivel_modalidad","id_escuela","id_nivel");
    }
    public function modalidades() {
        return $this->belongsToMany(Modalidad::class, "escuela_nivel_modalidad", "id_escuela", "id_modalidad");
    }
    public function otras_ofertas() {
        return $this->belongsToMany(Otras_Ofertas::class, "escuela_otras_ofertas","id_escuela", "id_otras_ofertas");
    }
    public function usuarios() {
        return $this->belongsToMany(Usuario::class, "usuario_escuela","id_escuela", "id_usuario");
    }
    public function propuestas_institucionales() {
        return $this->belongsToMany(Propuesta_Institucional::class,"escuela_PI","id_escuela","id_propuesta_institucional");
    }
    public function inscripciones_escuela_destino(){
        return $this->hasMany(Inscripcion::class,"id_escuela_destino","id");
    }
    public function inscripciones_escuela_procedencia(){
        return $this->hasMany(Inscripcion::class,"id_escuela_procedencia","id");
    }

}
