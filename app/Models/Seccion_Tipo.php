<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seccion_Tipo extends Model
{
    use HasFactory;

    protected $table = 'seccion_tipo';
    protected $fillable = ["nombre","orden"];

    public function espacios_academicos(){
        return $this->hasMany(Espacio_Academico::class,"id_seccion_tipo","id");
    }
}
