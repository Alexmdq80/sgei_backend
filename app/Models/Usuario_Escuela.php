<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario_Escuela extends Model
{
    use HasFactory;

    protected $table = "usuario_escuela";

    protected $fillable = ["id_escuela","id_usuario","es_admin","verificado"];

    public $timestamps = false;
    
}
