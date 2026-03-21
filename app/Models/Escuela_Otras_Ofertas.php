<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Escuela_Otras_Ofertas extends Model
{
    use HasFactory;

    protected $table = "escuela_otras_ofertas";

    public $timestamps = false;

    // public function escuela() {
    //     return $this->belongsTo(Escuela::class, "id", "id_escuela");
    // }
    // public function otras_ofertas() {
    //     return $this->belongsTo(Otras_Ofertas::class, "id", "id_otras_ofertas");
    // }

}
