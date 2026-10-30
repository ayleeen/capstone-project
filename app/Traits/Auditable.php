<?php

namespace App\Traits;

use App\Models\AuditLog;

trait Auditable
{
    protected array $auditHidden = ['password', 'remember_token', 'otp', 'otp_code'];

    protected static function bootAuditable(): void
    {
        static::created(function ($model) {
            AuditLog::record(
                'create', $model, null,
                $model->auditValues($model->getAttributes()),
                'Created ' . $model->auditLabel()
            );
        });

        static::updated(function ($model) {
            $changes = collect($model->getChanges())->except(['updated_at', 'deleted_at']);

            if ($changes->isEmpty()) {
                return;
            }

            $old = $changes->mapWithKeys(fn ($v, $k) => [$k => $model->getOriginal($k)])->all();

            AuditLog::record(
                'update', $model,
                $model->auditValues($old),
                $model->auditValues($changes->all()),
                'Updated ' . $model->auditLabel() . ' (' . $changes->keys()->implode(', ') . ')'
            );
        });

        static::deleted(function ($model) {
                \Log::info('CUSTOMER AUDIT EVENT FIRED', [
        'model' => get_class($model),
        'id' => $model->getKey(),
    ]);
            $permanent = method_exists($model, 'isForceDeleting') ? $model->isForceDeleting() : true;

            AuditLog::record(
                'delete', $model,
                $model->auditValues($model->getAttributes()),
                null,
                ($permanent ? 'Permanently deleted ' : 'Moved to archive: ') . $model->auditLabel()
            );
        });

        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                AuditLog::record('update', $model, null, null, 'Restored from archive: ' . $model->auditLabel());
            });
        }
    }

    protected function auditValues(array $values): array
    {
        return collect($values)->except($this->auditHidden)->all();
    }

    protected function auditLabel(): string
    {
        $name = $this->package_name ?? $this->name ?? $this->title ?? null;

        return class_basename($this) . ' #' . $this->getKey() . ($name ? " ({$name})" : '');
    }
}