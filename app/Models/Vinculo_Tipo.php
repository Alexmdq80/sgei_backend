<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vinculo_Tipo extends Model
{
    use HasFactory;

    protected $table = "vinculo_tipo";

    protected $fillable = ["nombre","orden","vigente"];

    public function adultos_vinculos() {
        return $this->hasMany(Adulto_Vinculo::class, "id_vinculo_tipo", "id" );
    }
}
