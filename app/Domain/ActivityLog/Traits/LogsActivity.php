<?php

namespace App\Domain\ActivityLog\Traits;

use App\Domain\ActivityLog\Interfaces\Services\ActivityLogServiceInterface;

trait LogsActivity
{
    public static function bootLogsActivity(): void
    {
        $modelName = strtolower(class_basename(static::class));

        static::created(function ($model) use ($modelName) {
            app(ActivityLogServiceInterface::class)
                ->logModelEvent($model, "{$modelName}.created");
        });

        static::updated(function ($model) use ($modelName) {
            app(ActivityLogServiceInterface::class)
                ->logModelEvent($model, "{$modelName}.updated", [
                    'old' => array_intersect_key(
                        $model->getOriginal(),
                        $model->getChanges()
                    ),
                    'new' => $model->getChanges(),
                ]);
        });

        static::deleted(function ($model) use ($modelName) {
            app(ActivityLogServiceInterface::class)
                ->logModelEvent($model, "{$modelName}.deleted");
        });
    }
}
