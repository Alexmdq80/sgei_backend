<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EscuelaUsuario extends Model
{
    use SoftDeletes, HasUuids; // AuditableTrait removed for debugging

    protected $table = 'escuela_usuario';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    protected $fillable = [
        "id",
        "escuela_id",
        "usuario_id",
        "verified_at",
        "rol_escolar_id"
    ];

    protected $casts = [
       'verified_at' => 'datetime'
    ];

    public function escuela(): BelongsTo
    {
        return $this->belongsTo(Escuela::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }

    public function rolEscolar(): BelongsTo
    {
        return $this->belongsTo(RolEscolar::class, 'rol_escolar_id');
    }
}
