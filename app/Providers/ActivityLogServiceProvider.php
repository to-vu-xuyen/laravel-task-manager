<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\ActivityLog\Interfaces\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ActivityLog\Repositories\ActivityLogRepository;
use App\Domain\ActivityLog\Interfaces\Services\ActivityLogServiceInterface;
use App\Domain\ActivityLog\Services\ActivityLogService;

class ActivityLogServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // ActivityLog Repositories
        $this->app->bind(ActivityLogRepositoryInterface::class, ActivityLogRepository::class);

        // ActivityLog Services
        $this->app->bind(ActivityLogServiceInterface::class, ActivityLogService::class);
    }

    public function boot(): void
    {
        //
    }
}
