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

    /**
     * Relationship to the district associated with the user (Jefe Distrital).
     */
    public function distritoUsuario(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(DistritoUsuario::class, 'usuario_id');
    }

    /**
     * Relationship to the province associated with the user (Jefe Provincial).
     */
    public function provinciaUsuario(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ProvinciaUsuario::class, 'usuario_id');
    }

    /**
     * Relationship to the region associated with the user (Jefe Regional).
     */
    public function regionUsuario(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(RegionUsuario::class, 'usuario_id');
    }

    /**
     * Scope: Usuarios dentro de una provincia (vía todas las rutas geográficas).
     */
    public function scopeInProvincia($query, int $provinciaId): void
    {
        $query->where(function ($q) use ($provinciaId) {
            $q->whereHas('provinciaUsuario', function ($pq) use ($provinciaId) {
                $pq->where('provincia_id', $provinciaId);
            })
                ->orWhereHas('regionUsuario.region', function ($rq) use ($provinciaId) {
                    $rq->where('provincia_id', $provinciaId);
                })
                ->orWhereHas('distritoUsuario.distrito', function ($dq) use ($provinciaId) {
                    $dq->where('provincia_id', $provinciaId);
                })
                ->orWhereHas('persona.escuelasPersonas.escuela.localidad.departamento', function ($ld) use ($provinciaId) {
                    $ld->where('provincia_id', $provinciaId);
                })
                ->orWhereHas('persona.movimientosCupofActivos.cupof.escuela.localidad.departamento', function ($sq) use ($provinciaId) {
                    $sq->where('provincia_id', $provinciaId);
                })
                ->orWhereHas('persona.inscripcion.escuelaProcedencia.localidad.departamento', function ($sq) use ($provinciaId) {
                    $sq->where('provincia_id', $provinciaId);
                })
                ->orWhereHas('persona.vinculosComoAdulto', function ($q) use ($provinciaId) {
                    $q->whereHas('inscripcion.escuelaProcedencia.localidad.departamento', function ($sq) use ($provinciaId) {
                        $sq->where('provincia_id', $provinciaId);
                    });
                })
                ->orWhere(function ($sq) use ($provinciaId) {
                    $sq->where('estado', 'vinculacion_pendiente')
                        ->whereExists(function ($ex) use ($provinciaId) {
                            $ex->select(\DB::raw(1))
                                ->from('personas')
                                ->join('cupof_movimientos', 'personas.id', '=', 'cupof_movimientos.persona_id')
                                ->join('cupofs', 'cupof_movimientos.cupof_id', '=', 'cupofs.id')
                                ->join('escuelas', 'cupofs.escuela_id', '=', 'escuelas.id')
                                ->join('localidads', 'escuelas.localidad_id', '=', 'localidads.id')
                                ->join('departamentos', 'localidads.departamento_id', '=', 'departamentos.id')
                                ->join('regions', 'departamentos.region_id', '=', 'regions.id')
                                ->whereColumn('personas.documento_numero', 'usuarios.documento_numero')
                                ->whereColumn('personas.documento_tipo_id', 'usuarios.documento_tipo_id')
                                ->where('cupof_movimientos.activo', true)
                                ->where('regions.provincia_id', $provinciaId);
                        });
                });
        });
    }

    /**
     * Scope: Usuarios dentro de una región (vía todas las rutas geográficas).
     */
    public function scopeInRegion($query, int $regionId): void
    {
        $query->where(function ($q) use ($regionId) {
            $q->whereHas('regionUsuario', function ($rq) use ($regionId) {
                $rq->where('region_id', $regionId);
            })
                ->orWhereHas('distritoUsuario.distrito', function ($dq) use ($regionId) {
                    $dq->where('region_id', $regionId);
                })
                ->orWhereHas('persona.escuelasPersonas.escuela.localidad.departamento', function ($ld) use ($regionId) {
                    $ld->where('region_id', $regionId);
                })
                ->orWhereHas('persona.movimientosCupofActivos.cupof.escuela.localidad.departamento', function ($sq) use ($regionId) {
                    $sq->where('region_id', $regionId);
                })
                ->orWhereHas('persona.inscripcion.escuelaProcedencia.localidad.departamento', function ($sq) use ($regionId) {
                    $sq->where('region_id', $regionId);
                })
                ->orWhereHas('persona.vinculosComoAdulto', function ($q) use ($regionId) {
                    $q->whereHas('inscripcion.escuelaProcedencia.localidad.departamento', function ($sq) use ($regionId) {
                        $sq->where('region_id', $regionId);
                    });
                })
                ->orWhere(function ($sq) use ($regionId) {
                    $sq->where('estado', 'vinculacion_pendiente')
                        ->whereExists(function ($ex) use ($regionId) {
                            $ex->select(\DB::raw(1))
                                ->from('personas')
                                ->join('cupof_movimientos', 'personas.id', '=', 'cupof_movimientos.persona_id')
                                ->join('cupofs', 'cupof_movimientos.cupof_id', '=', 'cupofs.id')
                                ->join('escuelas', 'cupofs.escuela_id', '=', 'escuelas.id')
                                ->join('localidads', 'escuelas.localidad_id', '=', 'localidads.id')
                                ->join('departamentos', 'localidads.departamento_id', '=', 'departamentos.id')
                                ->whereColumn('personas.documento_numero', 'usuarios.documento_numero')
                                ->whereColumn('personas.documento_tipo_id', 'usuarios.documento_tipo_id')
                                ->where('cupof_movimientos.activo', true)
                                ->where('departamentos.region_id', $regionId);
                        });
                });
        });
    }

    /**
     * Scope: Usuarios dentro de un departamento (vía todas las rutas geográficas).
     */
    public function scopeInDepartamento($query, int $departamentoId): void
    {
        $query->where(function ($q) use ($departamentoId) {
            $q->whereHas('distritoUsuario', function ($dq) use ($departamentoId) {
                $dq->where('departamento_id', $departamentoId);
            })
                ->orWhereHas('persona.escuelasPersonas.escuela.localidad', function ($l) use ($departamentoId) {
                    $l->where('departamento_id', $departamentoId);
                })
                ->orWhereHas('persona.movimientosCupofActivos.cupof.escuela.localidad.departamento', function ($sq) use ($departamentoId) {
                    $sq->where('departamento_id', $departamentoId);
                })
                ->orWhereHas('persona.inscripcion.escuelaProcedencia.localidad.departamento', function ($sq) use ($departamentoId) {
                    $sq->where('departamento_id', $departamentoId);
                })
                ->orWhereHas('persona.vinculosComoAdulto', function ($q) use ($departamentoId) {
                    $q->whereHas('inscripcion.escuelaProcedencia.localidad.departamento', function ($sq) use ($departamentoId) {
                        $sq->where('departamento_id', $departamentoId);
                    });
                })
                ->orWhere(function ($sq) use ($departamentoId) {
                    $sq->where('estado', 'vinculacion_pendiente')
                        ->whereExists(function ($ex) use ($departamentoId) {
                            $ex->select(\DB::raw(1))
                                ->from('personas')
                                ->join('cupof_movimientos', 'personas.id', '=', 'cupof_movimientos.persona_id')
                                ->join('cupofs', 'cupof_movimientos.cupof_id', '=', 'cupofs.id')
                                ->join('escuelas', 'cupofs.escuela_id', '=', 'escuelas.id')
                                ->join('localidads', 'escuelas.localidad_id', '=', 'localidads.id')
                                ->whereColumn('personas.documento_numero', 'usuarios.documento_numero')
                                ->whereColumn('personas.documento_tipo_id', 'usuarios.documento_tipo_id')
                                ->where('cupof_movimientos.activo', true)
                                ->where('localidads.departamento_id', $departamentoId);
                        });
                });
        });
    }
}
