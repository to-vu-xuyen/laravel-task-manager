<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Task\Interfaces\Services\TaskServiceInterface;
use App\Domain\Task\Interfaces\Services\TaskCacheServiceInterface;
use App\Domain\Task\Interfaces\Repositories\TaskRepositoryInterface;
use App\Domain\Task\Services\TaskService;
use App\Domain\Task\Services\TaskCacheService;
use App\Domain\Task\Repositories\TaskRepository;
use App\Domain\Task\Repositories\CachingTaskRepository;
use Illuminate\Cache\CacheManager;
use Illuminate\Support\Facades\Event;
use App\Domain\Task\Listeners\TaskCacheEventSubscriber;

class TaskServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Repository: TaskRepository → CachingTaskRepository (Decorator)
        $this->app->bind(TaskRepositoryInterface::class, function ($app) {
            return new CachingTaskRepository(
                $app->make(TaskRepository::class),
                $app->make(CacheManager::class)
            );
        });

        $this->app->bind(TaskServiceInterface::class, TaskService::class);
        $this->app->bind(TaskCacheServiceInterface::class, TaskCacheService::class);
    }


    public function boot(): void
    {
        //
        Event::subscribe(TaskCacheEventSubscriber::class);
    }
}
