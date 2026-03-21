<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Adulto_Vinculo extends Model
{
    use HasFactory;

    protected $table = "adulto_vinculo";

    protected $fillable = ["nombre","orden","vigente"];

    public function adultos(){
        return $this->hasMany(Estudiante_Adulto_Vinculo::class,"id_persona_adulto","id");
    }
    public function estudiantes(){
        return $this->hasMany(Estudiante_Adulto_Vinculo::class,"id_persona_estudiante","id");
    }
    public function vinculo_tipo(){
        return $this->belongsTo(Vinculo_Tipo::class, "id_vinculo_tipo");
    }
    public function estudiantes_adultos_vinculos() {
        return $this->hasMany(Estudiante_Adulto_Vinculo::class, "id_adulto_vinculo", "id" );
    }

}
