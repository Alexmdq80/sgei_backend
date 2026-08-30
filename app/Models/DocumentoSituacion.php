<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $nombre
 * @property int $vigente
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Persona> $personas
 * @property-read int|null $personas_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoSituacion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoSituacion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoSituacion onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoSituacion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoSituacion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoSituacion whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoSituacion whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoSituacion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoSituacion whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoSituacion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoSituacion whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoSituacion whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoSituacion withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoSituacion withoutTrashed()
 * @mixin \Eloquent
 */
class DocumentoSituacion extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "nombre",
        "vigente"
    ];

    /**
     * Relationship to the people.
     */
    public function personas(): HasMany
    {
        return $this->hasMany(Persona::class);
    }
}
