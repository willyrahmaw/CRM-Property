<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLogService
{
    /**
     * Log a sensitive or important business action.
     */
    public function log(
        string $action,
        Model|string $entity,
        ?array $before = null,
        ?array $after = null,
        ?string $entityId = null
    ): AuditLog {
        $user = Auth::user();
        $entityType = is_string($entity) ? $entity : get_class($entity);
        $resolvedEntityId = $entityId ?? ($entity instanceof Model ? (string) $entity->getKey() : null);

        // Security rule: Never store passwords, tokens or sensitive keys in audit logs
        $sanitizedBefore = $this->sanitizePayload($before);
        $sanitizedAfter = $this->sanitizePayload($after);

        return AuditLog::create([
            'company_id' => $user?->company_id,
            'user_id' => $user?->id,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $resolvedEntityId,
            'before_values' => $sanitizedBefore,
            'after_values' => $sanitizedAfter,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }

    /**
     * Remove sensitive keys from audit payloads.
     */
    protected function sanitizePayload(?array $data): ?array
    {
        if ($data === null) {
            return null;
        }

        $redactedKeys = ['password', 'remember_token', 'token', 'secret', 'api_key'];

        foreach ($redactedKeys as $key) {
            if (array_key_exists($key, $data)) {
                $data[$key] = '[REDACTED]';
            }
        }

        return $data;
    }
}
