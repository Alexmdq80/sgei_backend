<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agente extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $auditGroup = 'entities';

    protected $fillable = [
        'persona_id',
        'legajo',
        'fecha_ingreso_sistema',
        'estado_administrativo'
    ];

    protected $casts = [
        'fecha_ingreso_sistema' => 'date'
    ];

    /**
     * Relationship to the persona.
     */
    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }
}
