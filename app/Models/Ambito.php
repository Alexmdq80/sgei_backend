<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ambito extends Model
{
    use HasFactory;

    protected $table = "ambito";
    protected $fillable = ["nombre","orden","vigente"];   

    public function escuelas() {
        return $this->hasMany(Escuela::class, "id_ambito", "id" );
    }

    

}
