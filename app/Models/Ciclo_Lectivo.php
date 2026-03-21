<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ciclo_Lectivo extends Model
{
    use HasFactory;

    protected $table = "ciclo_lectivo";
    protected $fillable = ["nombre","orden","vigente","cerrado"];

    public $timestamps = false;

    public function propuestas_institucionales(){
        return $this->hasMany(Propuesta_Academico::class,"id_ciclo_lectivo","id");
    }
    public function espacios_academicos(){
        return $this->hasMany(Espacio_Academico::class,"id_ciclo_lectivo","id");
    }
    public function inscripciones(){
        return $this->hasMany(Inscripcion::class,"id_ciclo_lectivo","id");
    }
   
}
