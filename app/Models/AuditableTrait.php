<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait AuditableTrait
{
    /**
     * Boot the trait to handle automatic auditing fields.
     */
    protected static function bootAuditableTrait(): void
    {
        static::creating(function ($model) {
            if (Auth::check()) {
                if (!isset($model->created_by)) {
                    $model->created_by = Auth::id();
                }
                if (!isset($model->updated_by)) {
                    $model->updated_by = Auth::id();
                }
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });
    }

    /**
     * Relationship to the user who created the record.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'created_by');
    }

    /**
     * Relationship to the user who last updated the record.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'updated_by');
    }
}
