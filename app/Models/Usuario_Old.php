<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario_Old extends Model
{
    use HasFactory;

    protected $table = "usuario";

    protected $fillable = ["nombre_usuario","nombre","apellido","clave"];

    protected $hidden = ["clave"];

    // public function persona(){
    //     return $this->hasOne(Persona::class, "id", "id_persona");
    // }

    public function inscripciones(){
        return $this->hasMany(Inscripcion::class, "id_usuario", "id");
    }

    public function escuelas() {
        return $this->belongsToMany(Escuela::class, "usuario_escuela","id_usuario", "id_escuela");
    }

}
