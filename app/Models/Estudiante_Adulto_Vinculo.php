<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estudiante_Adulto_Vinculo extends Model
{
    use HasFactory;

    protected $table = "estudiante_adulto_vinculo";

    protected $fillable = ["detalle","vencimiento_fecha"];

    public function adulto_vinculo(){
        return $this->belongsTo(Adulto_Vinculo::class, "id_adulto_vinculo");
    }

    public function inscripcion_firma(){
      return $this->hasMany(Inscripcion::class,"id_persona_firma","id");
    }
    
    public function inscripcion_responsable_1(){
      return $this->hasMany(Inscripcion::class,"responsable_1","id");
    }
    
    public function inscripcion_responsable_2(){
      return $this->hasMany(Inscripcion::class,"responsable_2","id");
    }
    
    public function inscripcion_restringida(){
      return $this->hasMany(Inscripcion::class,"restringida","id");
    }

}
