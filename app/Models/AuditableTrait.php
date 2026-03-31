<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

trait AuditableTrait
{
    /**
     * Boot the trait to handle automatic auditing fields and event logs.
     */
    protected static function bootAuditableTrait(): void
    {
        // 1. Automatic author fields (created_by, updated_by)
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

        // 2. Event Logging (created, updated, deleted)
        static::created(function ($model) {
            $model->logAudit('created', null, $model->getAttributes());
        });

        static::updated(function ($model) {
            $old = $model->getOriginal();
            $new = $model->getChanges();
            
            // Only log if there are relevant changes
            if (!empty($new)) {
                $model->logAudit('updated', $old, $new);
            }
        });

        static::deleted(function ($model) {
            $model->logAudit('deleted', $model->getOriginal(), null);
        });
    }

    /**
     * Log the audit entry to the appropriate table based on auditGroup.
     */
    protected function logAudit(string $event, ?array $old, ?array $new): void
    {
        $auditGroup = property_exists($this, 'auditGroup') ? $this->auditGroup : 'system';
        $tableName = "audit_{$auditGroup}";
        
        // Ensure the table exists or fallback to system
        if (!in_array($auditGroup, ['entities', 'academic', 'system'])) {
            $tableName = 'audit_system';
        }

        // Filter sensitive or irrelevant fields
        $filteredOld = $old ? $this->filterAuditFields($old) : null;
        $filteredNew = $new ? $this->filterAuditFields($new) : null;

        // Skip logging if no relevant fields changed (e.g. only updated_at)
        if ($event === 'updated' && empty($filteredNew)) {
            return;
        }

        DB::table($tableName)->insert([
            'auditable_type' => get_class($this),
            'auditable_id' => $this->id,
            'event' => $event,
            'old_values' => $filteredOld ? json_encode($filteredOld) : null,
            'new_values' => $filteredNew ? json_encode($filteredNew) : null,
            'url' => Request::fullUrl(),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'user_id' => Auth::id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Remove sensitive or technical fields from audit logs.
     */
    protected function filterAuditFields(array $data): array
    {
        $ignore = [
            'password', 'remember_token', 'created_at', 'updated_at', 
            'deleted_at', 'created_by', 'updated_by'
        ];

        return array_diff_key($data, array_flip($ignore));
    }
}
