<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProvinciaUsuario extends Model
{
    use HasFactory, SoftDeletes, HasUuids, AuditableTrait;

    protected $table = 'provincia_usuario';

    protected $fillable = [
        'usuario_id',
        'provincia_id'
    ];

    /**
     * Relationship to the user (Jefe Provincial).
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }

    /**
     * Relationship to the province.
     */
    public function provincia(): BelongsTo
    {
        return $this->belongsTo(Provincia::class);
    }
}
