<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documento_Situacion extends Model
{
    use HasFactory;

    protected $table = "documento_situacion";
    protected $fillable = ["nombre","orden","vigente"];   

    public function personas() {
        return $this->hasMany(Persona::class, "id_documento_situacion", "id" );
    }


}
