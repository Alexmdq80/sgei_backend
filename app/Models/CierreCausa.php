<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CierreCausa extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "nombre",
        "vigente",
        "created_by",
        "updated_by"
    ];

    /**
     * Relationship to the history extra info records.
     */
    public function historialInfoInscripciones(): HasMany
    {
        return $this->hasMany(HistorialInfoInscripcion::class);
    }
}
