<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nivel extends Model
{
    use HasFactory;

    protected $table = 'nivel';
    protected $fillable = ["nombre","orden","vigente"];

    // public function escuela_nivel_modalidad() {
    //     return $this->hasMany(Escuela_Nivel_Modalidad::class, "id_nivel", "id" );
    // }

    public function escuelas() {
        return $this->belongsToMany(Escuela::class, "escuela_nivel_modalidad","id_nivel","id_escuela");
    }

    public function inscripciones() {
        return $this->hasMany(Inscripcion::class, "id_nivel_procedencia", "id" );
    }

}
