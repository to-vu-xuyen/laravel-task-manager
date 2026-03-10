<?php
namespace App\Domain\Task\Listeners;
use App\Domain\Task\Events\TaskCreated;
use App\Domain\Task\Events\TaskStatusChanged;
use App\Domain\Task\Interfaces\Services\TaskCacheServiceInterface;
use Illuminate\Contracts\Events\Dispatcher;
class TaskCacheEventSubscriber
{
    public function __construct(
        private readonly TaskCacheServiceInterface $cacheService,
    ) {
    }

    public function handleTaskCreated(TaskCreated $event): void
    {
        $this->cacheService->flushAll();
    }

    public function handleTaskStatusChanged(TaskStatusChanged $event): void
    {
        $this->cacheService->flushAll();
    }

    public function subscribe(Dispatcher $events): array
    {
        return [
            TaskCreated::class => 'handleTaskCreated',
            TaskStatusChanged::class => 'handleTaskStatusChanged',
        ];
    }
}