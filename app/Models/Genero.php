<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genero extends Model
{
    use HasFactory;

    protected $table = "genero";

    protected $fillable = ["nombre","orden","vigente"];

    public function personas() {
        return $this->hasMany(Persona::class, "id_genero", "id" );
    }

}
