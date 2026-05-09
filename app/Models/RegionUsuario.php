<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegionUsuario extends Model
{
    use HasFactory, SoftDeletes, HasUuids, AuditableTrait;

    protected $table = 'region_usuario';

    protected $fillable = [
        'usuario_id',
        'region_id'
    ];

    /**
     * Relationship to the user (Jefe Regional).
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }

    /**
     * Relationship to the region.
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }
}
