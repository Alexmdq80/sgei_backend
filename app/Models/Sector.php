<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    use HasFactory;

    protected $table = 'sector';
    protected $fillable = ["nombre","orden","vigente"];   

    public function escuelas() {
        return $this->hasMany(Escuela::class, "id_sector", "id" );
    }

}
