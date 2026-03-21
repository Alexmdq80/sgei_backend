<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Funcion_Georef extends Model
{
    use HasFactory;

    protected $table = "funcion_georef";  
    protected $fillable = ["nombre","orden","vigente"];   

    public function localidades_censales() {
        return $this->hasMany(Localidad_Censal::class, "id_funcion_georef", "id" );
    }

    public $timestamps = false;

}
