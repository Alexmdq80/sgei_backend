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
 * @property string $nombre
 * @property int|null $documento_tipo_id
 * @property string|null $documento_numero
 * @property bool $es_administrador
 * @property string $estado
 * @property string $email
 * @property string|null $avatar_path
 * @property string $password
 * @property bool $password_set
 * @property string|null $verification_token
 * @property string|null $remember_token
 * @property Carbon|null $email_set_at
 * @property int $email_correction_attempts
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \App\Models\DocumentoTipo|null $documentoTipo
 * @property-read string|null $avatar_url
 * @property-read bool $has_password
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \App\Models\Persona|null $persona
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RefreshToken> $refreshTokens
 * @property-read int|null $refresh_tokens_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UsuarioFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario permission($permissions, bool $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario role($roles, ?string $guard = null, bool $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereAvatarPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereDocumentoNumero($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereDocumentoTipoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereEmailCorrectionAttempts($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereEmailSetAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereEsAdministrador($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario wherePasswordSet($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereVerificationToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereVerificationTokenCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario withoutRole($roles, ?string $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario withoutTrashed()
 * @mixin \Eloquent
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
        return $this->avatar_path ? url("/api/v1/usuarios/{$this->id}/avatar") : null;
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
