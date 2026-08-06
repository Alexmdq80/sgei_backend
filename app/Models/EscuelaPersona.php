<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Models\Role;

class EscuelaPersona extends Model
{
    use SoftDeletes, HasUuids, HasFactory, AuditableTrait;

    protected $table = 'escuela_persona';

    /**
     * Group for segmented auditing.
     *
     * @var string
     */
    protected $auditGroup = 'entities';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        "id",
        "escuela_id",
        "persona_id",
        "verified_at",
        "role_id",
        "created_by",
        "updated_by"
    ];

    protected $casts = [
       'verified_at' => 'datetime'
    ];

    public function escuela(): BelongsTo
    {
        return $this->belongsTo(Escuela::class);
    }

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
