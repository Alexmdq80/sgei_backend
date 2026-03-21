<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ciclo_plan_estudio extends Model
{
    use HasFactory;

    protected $table = "ciclo_plan_estudio";

    protected $fillable = ["nombre","orden"];

    public function planes_estudio(){
        return $this->hasMany(Plan_Estudio::class,"id_ciclo_plan_estudio","id");
    }

    public function anios(){
        return $this->hasMany(Anio::class,"id_ciclo_plan_estudio","id");
    }

    public function anios_planes(){
        return $this->hasMany(Anio_Plan::class,"id_ciclo_plan_estudio","id");
    }

    public function propuestas_institucionales(){
        return $this->hasMany(Propuesta_Institucional::class,"id_ciclo_plan_estudio","id");
    }

    public function Espacios_Academicos(){
        return $this->hasMany(Espacio_Academico::class,"id_ciclo_plan_estudio","id");
    }

}
