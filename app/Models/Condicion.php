<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Condicion extends Model
{
    use HasFactory;

    protected $table = "condicion";
    protected $fillable = ["nombre","vigente"];

    public function inscripciones(){
        return $this->hasMany(Inscripcion::class,"id_condicion","id");
    } 

}
