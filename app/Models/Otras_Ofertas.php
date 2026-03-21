<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otras_Ofertas extends Model
{
    use HasFactory;

    protected $table = "otras_ofertas";

    protected $fillable = ["nombre","orden","vigente"];

    // public function escuela_otras_ofertas() {
    //     return $this->hasMany(Escuela_Otras_Ofertas::class, "id_otras_ofertas", "id" );
    // }

    public function escuelas() {
        return $this->belongsToMany(Escuela::class, "escuela_otras_ofertas","id_otras_ofertas", "id_escuela");
    }


}
