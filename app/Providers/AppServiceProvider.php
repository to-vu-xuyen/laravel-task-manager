<?php

namespace App\Providers;

use App\Domain\ActivityLog\Interfaces\Services\ActivityLogServiceInterface;
use App\Domain\ActivityLog\Services\ActivityLogService;
use App\Domain\ActivityLog\Interfaces\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ActivityLog\Repositories\ActivityLogRepository;

use App\Domain\Task\Interfaces\Services\TaskServiceInterface;
use App\Domain\Task\Services\TaskService;
use App\Domain\Task\Interfaces\Services\TaskCacheServiceInterface;
use App\Domain\Task\Services\TaskCacheService;

// use App\Domain\Task\Interfaces\Services\TaskAttachmentServiceInterface;
// use App\Domain\Task\Services\TaskAttachmentService;
use App\Domain\Task\Interfaces\Repositories\TaskRepositoryInterface;
use App\Domain\Task\Repositories\TaskRepository;
use App\Domain\Task\Repositories\CachingTaskRepository;
use App\Domain\Task\Models\Task;
use App\Domain\Task\Listeners\TaskCacheEventSubscriber;

use Illuminate\Cache\CacheManager;
use Illuminate\Support\Facades\Event;


use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Task Domain
        // $this->app->bind(TaskServiceInterface::class, TaskService::class);
        // // $this->app->bind(TaskRepositoryInterface::class, TaskRepository::class);
        // $this->app->bind(TaskRepositoryInterface::class, function ($app) {
        //     return new CachingTaskRepository(
        //         taskRepository: new TaskRepository($app->make(Task::class)),
        //         cache: $app->make(CacheManager::class),
        //     );
        // });
        // $this->app->bind(TaskCacheServiceInterface::class, TaskCacheService::class);


        // Task Attachment Domain
        // $this->app->bind(TaskAttachmentServiceInterface::class, TaskAttachmentService::class);
        // $this->app->bind(TaskAttachmentRepositoryInterface::class, TaskAttachmentRepository::class);


        // ActivityLog Domain
        // $this->app->bind(ActivityLogServiceInterface::class, ActivityLogService::class);
        // $this->app->bind(ActivityLogRepositoryInterface::class, ActivityLogRepository::class);
    }


    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
