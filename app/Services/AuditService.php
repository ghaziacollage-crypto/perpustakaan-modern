<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class AuditService
{
    public function log(Model $model, string $action, ?array $changes = null): AuditLog
    {
        return AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'changes' => $changes,
            'ip_address' => Request::ip(),
        ]);
    }

    public function logCreate(Model $model): AuditLog
    {
        return $this->log($model, 'create', [
            'created' => $model->getAttributes(),
        ]);
    }

    public function logUpdate(Model $model, array $oldValues, array $newValues): AuditLog
    {
        $changes = [];
        foreach ($newValues as $key => $value) {
            if (array_key_exists($key, $oldValues) && $oldValues[$key] !== $value) {
                $changes[$key] = [
                    'old' => $oldValues[$key],
                    'new' => $value,
                ];
            }
        }

        return $this->log($model, 'update', $changes);
    }

    public function logDelete(Model $model): AuditLog
    {
        return $this->log($model, 'delete', [
            'deleted' => $model->getAttributes(),
        ]);
    }

    public function logCustom(Model $model, string $action, array $changes): AuditLog
    {
        return $this->log($model, $action, $changes);
    }
}