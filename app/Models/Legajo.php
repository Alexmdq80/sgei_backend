<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Legajo extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $auditGroup = "entities";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "persona_id",
        "escuela_id",
        "libro",
        "folio",
        "legajo"
    ];

    /**
     * Relationship to the person (student).
     */
    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }

    /**
     * Relationship to the school.
     */
    public function escuela(): BelongsTo
    {
        return $this->belongsTo(Escuela::class);
    }
}
