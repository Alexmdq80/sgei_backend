<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anio_Plan extends Model
{
    use HasFactory;

    protected $table = "anio_plan";

    public function plan_estudio(){
        return $this->belongsTo(Plan_Estudio::class,"id_plan_estudio");
    }

    public function ciclo_plan_estudio(){
        return $this->belongsTo(ciclo_plan_estudio::class,"id_ciclo_plan_estudio");
    }

    public function anio(){
        return $this->belongsTo(Anio::class,"id_anio");
    }

    public function propuestas_institucionales(){
        return $this->hasMany(Propuesta_Insitucional::class,"id_anio_plan","id");
    }
    
    public function espacios_academicos(){
        return $this->hasMany(Espacio_Academico::class,"id_anio_plan","id");
    }
}
