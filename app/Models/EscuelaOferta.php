<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property-read \App\Models\Escuela|null $escuela
 * @property-read \App\Models\Oferta|null $oferta
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaOferta newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaOferta newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaOferta onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaOferta query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaOferta withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaOferta withoutTrashed()
 * @mixin \Eloquent
 */
class EscuelaOferta extends Pivot
{
    use HasFactory, SoftDeletes, AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "escuela_id",
        "oferta_id"
    ];

    /**
     * Relationship to the school.
     */
    public function escuela(): BelongsTo
    {
        return $this->belongsTo(Escuela::class);
    }

    /**
     * Relationship to the offer.
     */
    public function oferta(): BelongsTo
    {
        return $this->belongsTo(Oferta::class);
    }
}
