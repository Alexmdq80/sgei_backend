<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * @property int $id
 * @property int|null $persona_id
 * @property int|null $nacion_id
 * @property int|null $provincia_id
 * @property int|null $departamento_id
 * @property int|null $localidad_id
 * @property int|null $calle_id
 * @property int|null $calle_entre_1_id
 * @property int|null $calle_entre_2_id
 * @property string|null $numero
 * @property string|null $piso
 * @property string|null $torre
 * @property string|null $unidad
 * @property string|null $observaciones
 * @property string|null $codigo_postal
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Persona|null $persona
 * @property-read \App\Models\Nacion|null $nacion
 * @property-read \App\Models\Provincia|null $provincia
 * @property-read \App\Models\Departamento|null $departamento
 * @property-read \App\Models\Localidad|null $localidad
 * @property-read \App\Models\Calle|null $calle
 * @property-read \App\Models\Calle|null $entreCalle1
 * @property-read \App\Models\Calle|null $entreCalle2
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio whereCalleEntre1Id($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio whereCalleEntre2Id($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio whereCalleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio whereCodigoPostal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio whereDepartamento($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio whereLocalidadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio whereNumero($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio whereOtros($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio wherePersonaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio wherePiso($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio whereTorre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio withoutTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio whereNacionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio whereObservaciones($value)
($value)
 * @mixin \Eloquent
 */
class Domicilio extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $auditGroup = "entities";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "persona_id",
        "nacion_id",
        "provincia_id",
        "departamento_id",
        "localidad_id",
        "calle_id",
        "calle_entre_1_id",
        "calle_entre_2_id",
        "numero",
        "piso",
        "torre",
        "unidad",
        "observaciones",
        "codigo_postal"
    ];

    /**
     * Mutator for numero (Uppercase).
     */
    protected function numero(): Attribute
    {
        return Attribute::make(
            set: fn(?string $value) => $value ? mb_strtoupper(trim($value), 'UTF-8') : null,
        );
    }

    /**
     * Mutator for piso (Uppercase).
     */
    protected function piso(): Attribute
    {
        return Attribute::make(
            set: fn(?string $value) => $value ? mb_strtoupper(trim($value), 'UTF-8') : null,
        );
    }

    /**
     * Mutator for torre (Uppercase).
     */
    protected function torre(): Attribute
    {
        return Attribute::make(
            set: fn(?string $value) => $value ? mb_strtoupper(trim($value), 'UTF-8') : null,
        );
    }

    /**
     * Mutator for departamento (Uppercase).
     */
    protected function unidad(): Attribute
    {
        return Attribute::make(
            set: fn(?string $value) => $value ? mb_strtoupper(trim($value), 'UTF-8') : null,
        );
    }

    /**
     * Mutator for observaciones (Uppercase).
     */
    protected function observaciones(): Attribute
    {
        return Attribute::make(
            set: fn(?string $value) => $value ? mb_strtoupper(trim($value), 'UTF-8') : null,
        );
    }

    /**
     * Relationship to the person.
     */
    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }

    /**
     * Relationship to the nation of residence.
     * null => domicilio en Argentina (se aplica la cascada geográfica INDEC).
     */
    public function nacion(): BelongsTo
    {
        return $this->belongsTo(Nacion::class);
    }
    /**
     * Relación con la provincia de residencia (catálogo geográfico).
     */
    public function provincia(): BelongsTo
    {
        return $this->belongsTo(Provincia::class);
    }
    /**
     * Relación con el departamento (división administrativa) de residencia.
     * Sin ambigüedad: la unidad funcional (Dpto) vive en la columna `unidad`.
     */
    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }

    /**
     * 
     * Relationship to the locality.
     */
    public function localidad(): BelongsTo
    {
        return $this->belongsTo(Localidad::class);
    }

    /**
     * Relationship to the street.
     */
    public function calle(): BelongsTo
    {
        return $this->belongsTo(Calle::class);
    }

    /**
     * Relationship to the first intersection street.
     */
    public function entreCalle1(): BelongsTo
    {
        return $this->belongsTo(Calle::class, "calle_entre_1_id");
    }

    /**
     * Relationship to the second intersection street.
     */
    public function entreCalle2(): BelongsTo
    {
        return $this->belongsTo(Calle::class, "calle_entre_2_id");
    }
}
