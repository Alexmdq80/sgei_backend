<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string|null $auditable_type
 * @property string|null $auditable_id
 * @property string $event
 * @property string|null $attempted_email
 * @property string|null $url
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property array<array-key, mixed>|null $tags
 * @property array<array-key, mixed>|null $details
 * @property string $audit_driver
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationAudit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationAudit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationAudit onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationAudit query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationAudit whereAttemptedEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationAudit whereAuditDriver($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationAudit whereAuditableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationAudit whereAuditableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationAudit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationAudit whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationAudit whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationAudit whereDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationAudit whereEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationAudit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationAudit whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationAudit whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationAudit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationAudit whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationAudit whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationAudit whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationAudit withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationAudit withoutTrashed()
 * @mixin \Eloquent
 */
class AuthenticationAudit extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'authentication_audits';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'auditable_type',
        'auditable_id',
        'event',
        'attempted_email',
        'url',
        'ip_address',
        'user_agent',
        'tags',
        'details',
        'audit_driver',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tags' => 'array',
        'details' => 'array'
    ];
}
