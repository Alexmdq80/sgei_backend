<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jornada extends Model
{
    use HasFactory;

    protected $table = "jornada";

    protected $fillable = ["nombre","orden"];

    public function propuestas_institucionales(){
        return $this->hasMany(Propuesta_Institucional::class,"id_jornada","id");
    } 

}
