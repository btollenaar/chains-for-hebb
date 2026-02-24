<?php

namespace App\Models\Traits;

use App\Models\AuditLog;

trait Auditable
{
    protected static function bootAuditable(): void
    {
        static::created(function ($model) {
            if (app()->runningInConsole() && !app()->runningUnitTests()) {
                return;
            }
            AuditLog::record('created', $model, null, $model->getAttributes());
        });

        static::updated(function ($model) {
            if (app()->runningInConsole() && !app()->runningUnitTests()) {
                return;
            }
            $dirty = $model->getDirty();
            if (empty($dirty)) {
                return;
            }
            $old = array_intersect_key($model->getOriginal(), $dirty);
            AuditLog::record('updated', $model, $old, $dirty);
        });

        static::deleted(function ($model) {
            if (app()->runningInConsole() && !app()->runningUnitTests()) {
                return;
            }
            AuditLog::record('deleted', $model, $model->getOriginal());
        });
    }
}
