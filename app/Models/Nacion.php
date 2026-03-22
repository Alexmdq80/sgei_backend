<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Nacion extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "id_georef",
        "continente_id",
        "nombre",
        "nacionalidad"
    ];

    /**
     * Relationship to the continent.
     */
    public function continente(): BelongsTo
    {
        return $this->belongsTo(Continente::class);
    }

    /**
     * Relationship to the provinces in this nation.
     */
    public function provincias(): HasMany
    {
        return $this->hasMany(Provincia::class);
    }

    /**
     * Relationship to the people associated with this nation.
     */
    public function personas(): HasMany
    {
        return $this->hasMany(Persona::class);
    }

    /**
     * Relationship to the people with this nationality.
     */
    public function nacionalidadPersonas(): HasMany
    {
        return $this->hasMany(Persona::class, "nacionalidad_nacion_id", "id");
    }
}
