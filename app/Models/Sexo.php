<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sexo extends Model
{
    use HasFactory;
    protected $table = "sexo";
    protected $fillable = ["nombre","letra","orden","vigente"];

    public function personas() {
        return $this->hasMany(Persona::class, "id_sexo", "id" );
    }

                                

}
