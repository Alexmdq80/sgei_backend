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

use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Services\UserService;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property CarbonInterface|Carbon|null $verification_token_created_at
 * @property CarbonInterface|Carbon|null $email_verified_at
 */

class Usuario extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasUuids, AuditableTrait, HasRoles;

    /**
     * Group for segmented auditing.
     *
     * @var string
     */
    protected $auditGroup = 'entities';

    /**
     * Specified guard for Spatie roles and permissions.
     * 
     * @var string
     */
    protected $guard_name = 'sanctum';

    /**
     * Maximum number of times a user can change their email.
     */
    const MAX_EMAIL_CHANGES = 3;

    /**
     * Roles del equipo de conducción (directivos) en escuelas.
     */
    public const ROLES_EQUIPO_CONDUCCION = ['director', 'vicedirector', 'secretario', 'prosecretario'];


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'documento_tipo_id',
        'documento_numero',
        'email',
        'email_verified_at',
        'email_set_at',
        'email_correction_attempts',
        'password',
        'password_set',
        'verification_token',
        'verification_token_created_at',
        'avatar_path',
        'estado'
    ];

    /**
     * Relationship to the document type.
     */
    public function documentoTipo(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(DocumentoTipo::class);
    }

    /**
     * Relationship to the persona based on the explicit usuario_id in personas table.
     */
    public function persona(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Persona::class, 'usuario_id');
    }

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
     * Check if the user has a real password set (not a temporary invitation password).
     */
    public function getHasPasswordAttribute(): bool
    {
        return (bool) $this->password_set;
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
        'email_set_at' => 'datetime',
        'verification_token_created_at' => 'datetime',
        'password' => 'hashed',
        'password_set' => 'boolean',
        'es_administrador' => 'boolean',
        'email_correction_attempts' => 'integer',
    ];

    /**
     * Check if the verification token is expired (24 hours).
     */
    public function isVerificationTokenExpired(): bool
    {
        if (!$this->verification_token_created_at) {
            return true;
        }

        return $this->verification_token_created_at->addHours(24)->isPast();
    }

    /**
     * Check if the user can still change their email address.
     */
    public function canChangeEmail(): bool
    {
        return $this->email_correction_attempts < self::MAX_EMAIL_CHANGES;
    }

    /**
     * Mark the user's email as verified.
     */
    public function markEmailAsVerified(): void
    {
        $this->forceFill([
            'email_verified_at' => now(),
            'verification_token' => null,
            'verification_token_created_at' => null,
            'estado' => $this->es_administrador ? 'activo' : 'email_verificado',
        ])->save();

        // Intentar vincular con Persona automáticamente al verificar email
        app(UserService::class)->linkToPersona($this);
    }

    /**
     * Relationship to refresh tokens associated with the user.
     */
    public function refreshTokens(): HasMany
    {
        return $this->hasMany(RefreshToken::class);
    }

}
