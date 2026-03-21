<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dependencia extends Model
{
    use HasFactory;

    protected $table = 'dependencia';
    protected $fillable = ["nombre","orden","vigente"];   

    public function escuelas() {
        return $this->hasMany(Escuela::class, "id_dependencia", "id" );
    }

}
