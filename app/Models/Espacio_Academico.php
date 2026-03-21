<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Espacio_Academico extends Model
{
    use HasFactory;

    protected $table = 'espacio_academico';

    protected $fillable = ["division","nombre"];

    public function plan_estudio() {
        return $this->belongsTo(Plan_Estudio::class, "id_plan_estudio");
    }

    public function ciclo_plan_estudio() {
        return $this->belongsTo(ciclo_plan_estudio::class, "id_ciclo_plan_estudio");
    }

    public function anio() {
        return $this->belongsTo(Anio::class, "id_anio");
    }

    public function anio_plan() {
        return $this->belongsTo(Anio_Plan::class, "id_anio_plan");
    }

    public function propuesta_institucional() {
        return $this->belongsTo(Propuesta_Institucional::class, "id_propuesta_institucional");
    }

    public function turno_inicio() {
        return $this->belongsTo(Turno::class, "id_turno_inicio");
    }

    public function turno_fin() {
        return $this->belongsTo(Turno::class, "id_turno_fin");
    }

    public function ciclo_lectivo() {
        return $this->belongsTo(Ciclo_Lectivo::class, "id_ciclo_lectivo");
    }

    public function inscripciones(){
        return $this->hasMany(Inscripcion::class,"id_propuesta_institucional","id");
    }

}
