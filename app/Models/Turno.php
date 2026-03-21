<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Turno extends Model
{
    use HasFactory;

    protected $table = 'turno';

    protected $fillable = ["nombre","orden"];


    public function propuestas_institucionales_turno_inicio(){
        return $this->hasMany(Propuesta_Insitucional::class,"id_turno_inicio","id");
    }
    
    public function propuestas_institucionales_turno_fin(){
        return $this->hasMany(Propuesta_Insitucional::class,"id_turno_fin","id");
    }

    public function espacios_academicos_turno_inicio(){
        return $this->hasMany(Espacio_Academico::class,"id_turno_inicio","id");
    }
    
    public function espacios_academicos_turno_fin(){
        return $this->hasMany(Espacio_Academico::class,"id_turno_fin","id");
    }
}
