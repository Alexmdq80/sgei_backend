<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * @property int $id
 * @property string $nombre
 * @property string $tipo
 * @property int|null $escalafon_id
 * @property bool $requiere_cursos
 * @property bool $activo
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Escalafon|null $escalafon
 * @method static \Database\Factories\CargoFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cargo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cargo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cargo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cargo query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cargo whereActivo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cargo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cargo whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cargo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cargo whereEscalafonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cargo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cargo whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cargo whereRequiereCursos($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cargo whereTipo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cargo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cargo whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cargo withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cargo withoutTrashed()
 * @mixin \Eloquent
 */
class Cargo extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $auditGroup = 'entities';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'tipo',
        'escalafon_id',
        'requiere_cursos',
        'activo'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'requiere_cursos' => 'boolean',
        'activo' => 'boolean',
        'escalafon_id' => 'integer',
    ];

    /**
     * Relation with Escalafon.
     */
    public function escalafon(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Escalafon::class);
    }

    /**
     * Interact with the cargo's name.
     * Normalizes to uppercase for administrative standards.
     */
    protected function nombre(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => mb_strtoupper($value, 'UTF-8'),
        );
    }
}
