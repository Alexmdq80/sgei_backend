<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Legajo extends Model
{
    use HasFactory;
    
    protected $table = "legajo"; 
    protected $fillable = ["libro","folio","legajo"];

    public function persona() {
        return $this->belongsTo(Persona::class,"id_persona");
    }
    
}
