<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $persona_id
 * @property string|null $telefono_codigo_area
 * @property string|null $telefono
 * @property string|null $celular_codigo_area
 * @property string|null $celular
 * @property string|null $email
 * @property string|null $observaciones
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Persona|null $persona
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacto newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacto newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacto onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacto query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacto whereCelular($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacto whereCelularCodigoArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacto whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacto whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacto whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacto whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacto whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacto whereObservaciones($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacto wherePersonaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacto whereTelefono($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacto whereTelefonoCodigoArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacto whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacto whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacto withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacto withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Contacto extends Model
{
    use AuditableTrait, HasFactory, SoftDeletes;

    protected $auditGroup = 'entities';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'persona_id',
        'telefono_codigo_area',
        'telefono',
        'celular_codigo_area',
        'celular',
        'email',
        'observaciones',
    ];

    /**
     * Mutator for email (Lowercase).
     */
    protected function email(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => $value ? strtolower(trim($value)) : null,
        );
    }

    /**
     * Relationship to the person.
     */
    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }
}
