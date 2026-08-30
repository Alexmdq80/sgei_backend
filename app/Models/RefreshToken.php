<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $id
 * @property string $usuario_id
 * @property string $token
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property string|null $device_id
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Usuario|null $usuario
 * @method static Builder<static>|RefreshToken newModelQuery()
 * @method static Builder<static>|RefreshToken newQuery()
 * @method static Builder<static>|RefreshToken onlyTrashed()
 * @method static Builder<static>|RefreshToken query()
 * @method static Builder<static>|RefreshToken whereCreatedAt($value)
 * @method static Builder<static>|RefreshToken whereCreatedBy($value)
 * @method static Builder<static>|RefreshToken whereDeletedAt($value)
 * @method static Builder<static>|RefreshToken whereDeviceId($value)
 * @method static Builder<static>|RefreshToken whereExpiresAt($value)
 * @method static Builder<static>|RefreshToken whereId($value)
 * @method static Builder<static>|RefreshToken whereToken($value)
 * @method static Builder<static>|RefreshToken whereUpdatedAt($value)
 * @method static Builder<static>|RefreshToken whereUpdatedBy($value)
 * @method static Builder<static>|RefreshToken whereUsuarioId($value)
 * @method static Builder<static>|RefreshToken withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|RefreshToken withoutTrashed()
 * @mixin \Eloquent
 */
class RefreshToken extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait, Prunable;

    /**
     * Get the prunable model query.
     */
    public function prunable(): Builder
    {
        return static::withTrashed()
            ->where('expires_at', '<', now())
            ->orWhere('deleted_at', '<', now()->subDays(30));
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'usuario_id',
        'token',
        'expires_at',
        'device_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Relationship to the user who owns the refresh token.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }
}
