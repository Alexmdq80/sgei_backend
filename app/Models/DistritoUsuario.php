<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DistritoUsuario extends Model
{
    use HasFactory, SoftDeletes, HasUuids, AuditableTrait;

    protected $table = 'distrito_usuario';

    protected $fillable = [
        'usuario_id',
        'departamento_id'
    ];

    /**
     * Relationship to the user (Jefe Distrital).
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }

    /**
     * Relationship to the district (Departamento).
     */
    public function distrito(): BelongsTo
    {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }
}
