<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan_Estudio extends Model
{
    use HasFactory;

    protected $table = "plan_estudio";

    protected $fillable = ["nombre","nombre_completo","duracion_anios",
                           "resolucion","orientacion"];

    public function anios_planes(){
        return $this->hasMany(Anio_Plan::class,"id_plan_estudio","id");
    }

    public function propuestas_institucionales(){
        return $this->hasMany(Propuesta_Insitucional::class,"id_plan_estudio","id");
    }
    
    public function espacios_academicos(){
        return $this->hasMany(Espacio_Academico::class,"id_plan_estudio","id");
    }

    public function ciclo_plan_estudio(){
        return $this->belongsTo(ciclo_plan_estudio::class, "id_ciclo_plan_estudio");
    }
}
