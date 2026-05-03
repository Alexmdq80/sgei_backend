<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PuestoTipo extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'puesto_tipos';

    protected $fillable = [
        "nombre",
        "orden",
        "vigente"
    ];

    public function cupofs(): HasMany
    {
        return $this->hasMany(Cupof::class);
    }
}
