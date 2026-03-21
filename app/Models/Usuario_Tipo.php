<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario_Tipo extends Model
{
    // use HasFactory;
    protected $table = "usuario_tipo";
    protected $fillable = ["nombre","orden","vigente"];

    // public function personas() {
    //     return $this->hasMany(Persona::class, "id_sexo", "id" );
    // }



}
