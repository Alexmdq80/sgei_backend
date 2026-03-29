<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EscuelaUsuario extends \Illuminate\Database\Eloquent\Relations\Pivot implements AuditableContract
{
    use SoftDeletes, Auditable, HasUuids;

    protected $table = 'escuela_usuario';

    protected $fillable = [
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
