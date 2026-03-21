<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contacto extends Model
{
    use HasFactory;

    protected $table = "contacto"; 
    protected $fillable = ["telefono_codigo_area","telefono",
                           "celular_codigo_area","celular","email"
    ];

    public function persona() {
        return $this->belongsTo(Persona::class,"id_persona");
    }
    

}
