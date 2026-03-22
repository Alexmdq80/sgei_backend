<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable; // Importa el trait
use OwenIt\Auditing\Contracts\Auditable as AuditableContract; // Importa el contrato (necesario)
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

//class EscuelaUsuario  extends Model implements AuditableContract
class EscuelaUsuario extends \Illuminate\Database\Eloquent\Relations\Pivot implements AuditableContract
{
    use SoftDeletes, Auditable, HasUuids;

    protected $fillable = ["escuela_id",
                            "usuario_id",
                            "verified_at",
                            "usuario_tipo_id"
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
    public function usuarioTipo(): BelongsTo
    {
        return $this->belongsTo(UsuarioTipo::class);
    }

}
