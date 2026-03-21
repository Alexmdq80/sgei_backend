<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Domicilio extends Model
{
    use HasFactory;

    protected $table = 'domicilio';

    protected $fillable = ["numero","piso","torre","departamento",
                           "otros","codigo_postal"];

    public function persona() {
        return $this->belongsTo(Persona::class,"id_persona");
    }
    public function calle() {
        return $this->belongsTo(Calle::class,"id_calle");
    }
    public function calle_entre1() {
        return $this->belongsTo(Calle::class,"id_calle_entre1");
    }
    public function calle_entre2() {
        return $this->belongsTo(Calle::class,"id_calle_entre2");
    }
    public function localidad_asentamiento() {
        return $this->belongsTo(Localidad_Asentamiento::class,"id_localidad_asentamiento");
    }
    public function distrito() {
        return $this->belongsTo(Departamento::class,"id_departamento");
    }
    public function provincia() {
        return $this->belongsTo(Provincia::class,"id_provincia");
    }
    public function pais() {
        return $this->belongsTo(Pais::class,"id_pais");
    }


}
