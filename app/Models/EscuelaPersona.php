<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Models\Role;

/**
 * @property string $id
 * @property int $escuela_id
 * @property int $persona_id
 * @property \Illuminate\Support\Carbon|null $verified_at
 * @property int|null $role_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property-read \App\Models\Escuela|null $escuela
 * @property-read \App\Models\Persona|null $persona
 * @property-read Role|null $role
 * @method static \Database\Factories\EscuelaPersonaFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaPersona newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaPersona newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaPersona onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaPersona query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaPersona whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaPersona whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaPersona whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaPersona whereEscuelaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaPersona whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaPersona wherePersonaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaPersona whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaPersona whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaPersona whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaPersona whereVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaPersona withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaPersona withoutTrashed()
 * @mixin \Eloquent
 */
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
