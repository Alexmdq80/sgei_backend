<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Builder;

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
