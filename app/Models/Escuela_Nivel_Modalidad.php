<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Escuela_Nivel_Modalidad extends Model
{
    use HasFactory;

    protected $table = "escuela_nivel_modalidad";

    public $timestamps = false;

    // public function escuela() {
    //     return $this->belongsTo(Escuela::class, "id", "id_escuela");
    // }
    // public function nivel() {
    //     return $this->belongsTo(Nivel::class, "id", "id_nivel");
    // }
    // public function modalidad() {
    //     return $this->belongsTo(Modalidad::class, "id", "id_modalidad");
    // }

}
