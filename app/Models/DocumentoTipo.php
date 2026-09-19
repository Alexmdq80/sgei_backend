<?php

namespace App\Models;

use App\Traits\ActualizaVersionCatalogo;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $nombre
 * @property int $vigente
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Persona> $personas
 * @property-read int|null $personas_count
 *
 * @method static \Database\Factories\DocumentoTipoFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoTipo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoTipo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoTipo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoTipo query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoTipo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoTipo whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoTipo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoTipo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoTipo whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoTipo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoTipo whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoTipo whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoTipo withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoTipo withoutTrashed()
 *
 * @mixin \Eloquent
 */
class DocumentoTipo extends Model
{
    use ActualizaVersionCatalogo, AuditableTrait, HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'vigente',
    ];

    /**
     * Relationship to the people.
     */
    public function personas(): HasMany
    {
        return $this->hasMany(Persona::class);
    }
}
