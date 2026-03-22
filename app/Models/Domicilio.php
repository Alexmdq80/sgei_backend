<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Domicilio extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "persona_id",
        "localidad_id",
        "calle_id",
        "calle_entre_1_id",
        "calle_entre_2_id",
        "numero",
        "piso",
        "torre",
        "departamento",
        "otros",
        "codigo_postal"
    ];

    /**
     * Relationship to the person.
     */
    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }

    /**
     * Relationship to the locality.
     */
    public function localidad(): BelongsTo
    {
        return $this->belongsTo(Localidad::class);
    }

    /**
     * Relationship to the street.
     */
    public function calle(): BelongsTo
    {
        return $this->belongsTo(Calle::class);
    }

    /**
     * Relationship to the first intersection street.
     */
    public function entreCalle1(): BelongsTo
    {
        return $this->belongsTo(Calle::class, "calle_entre_1_id");
    }

    /**
     * Relationship to the second intersection street.
     */
    public function entreCalle2(): BelongsTo
    {
        return $this->belongsTo(Calle::class, "calle_entre_2_id");
    }
}
