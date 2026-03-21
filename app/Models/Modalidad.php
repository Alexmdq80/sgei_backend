<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modalidad extends Model
{
    use HasFactory;

    protected $table = 'modalidad';
    protected $fillable = ["nombre","orden","vigente"];
    // public function escuela_nivel_modalidad() {
    //     return $this->hasMany(Escuela_Nivel_Modalidad::class, "id_modalidad", "id" );
    // }

    public function escuelas() {
        return $this->belongsToMany(Escuela::class, "escuela_nivel_modalidad","id_modalidad", "id_escuela");
    }

    public function inscripciones() {
        return $this->hasMany(inscripcion::class, "id_modalidad_procedencia", "id" );
    }
    
}
