<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasRoles;

class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasUuids, AuditableTrait, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'password',
        'verification_token',
        'avatar_path'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = ['avatar_url'];

    /**
     * Get the full URL for the user's avatar.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar_path ? asset('storage/' . $this->avatar_path) : null;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Mark the user's email as verified.
     */
    public function markEmailAsVerified(): void
    {
        $this->forceFill([
            'email_verified_at' => now(),
            'verification_token' => null,
        ])->save();
    }

    /**
     * Relationship to schools associated with the user.
     */
    public function escuelaUsuarios(): HasMany
    {
        return $this->hasMany(EscuelaUsuario::class);
    }

    /**
     * Relationship to refresh tokens associated with the user.
     */
    public function refreshTokens(): HasMany
    {
        return $this->hasMany(RefreshToken::class);
    }
}
