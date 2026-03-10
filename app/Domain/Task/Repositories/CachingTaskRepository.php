<?php

namespace App\Domain\Task\Repositories;

use App\Domain\Task\Models\Task;
use App\Domain\Task\Cache\TaskCacheKeys;
use App\Domain\Task\Interfaces\Repositories\TaskRepositoryInterface;
use Illuminate\Cache\CacheManager;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CachingTaskRepository implements TaskRepositoryInterface
{
    protected const CACHE_KEY = 'tasks';

    public function __construct(
        private readonly TaskRepositoryInterface $taskRepository,
        private readonly CacheManager $cache
    ) {
    }

    // -------------------------------------------------------
    // Read Methods — check cache → miss → delegate → store
    // -------------------------------------------------------
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        $page = Paginator::resolveCurrentPage();
        $key = TaskCacheKeys::allTasksPaginated($page, $perPage);
        return $this->cache
            ->tags([TaskCacheKeys::TAG_ALL_TASKS])
            ->remember($key, TaskCacheKeys::DEFAULT_TTL, function () use ($perPage) {
                return $this->taskRepository->getAllPaginated($perPage);
            });
    }


    public function getByUserPaginated(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        $page = Paginator::resolveCurrentPage();
        $key = TaskCacheKeys::userTasksPaginated($userId, $page, $perPage);
        return $this->cache
            ->tags([TaskCacheKeys::TAG_ALL_TASKS, TaskCacheKeys::tagForUser($userId)])
            ->remember($key, TaskCacheKeys::DEFAULT_TTL, function () use ($userId, $perPage) {
                return $this->taskRepository->getByUserPaginated($userId, $perPage);
            });
    }

    public function findOrFail(int $id): Task
    {
        // $key = TaskCacheKeys::taskDetail($id);
        // Bước 1: Thử lấy từ cache trước (dùng TAG_ALL_TASKS chung vì chưa biết userId)
        // $cachedTask = $this->cache
        //     ->tags([TaskCacheKeys::TAG_ALL_TASKS])
        //     ->get($key);
        // if ($cachedTask !== null) {
        //     return $cachedTask;
        // }
        // Bước 2: Cache miss → query DB → lấy userId → cache với đầy đủ tags
        $task = $this->taskRepository->findOrFail($id);
        // $this->cache
        //     ->tags([
        //         TaskCacheKeys::TAG_ALL_TASKS,
        //         TaskCacheKeys::tagForUser($task->user_id),
        //         TaskCacheKeys::tagForItem($id),
        //     ])
        //     ->put($key, $task, TaskCacheKeys::DEFAULT_TTL);
        return $task;
    }

    public function create(array $data): Task
    {
        $task = $this->taskRepository->create($data);
        // Task mới → flush tất cả list caches (sort order có thể thay đổi)
        $this->cache->tags([TaskCacheKeys::TAG_ALL_TASKS])->flush();
        return $task;
    }
    public function update(int $id, array $data): bool
    {
        $result = $this->taskRepository->update($id, $data);
        // Data thay đổi → flush all (vì ảnh hưởng list + detail)
        $this->cache->tags([TaskCacheKeys::TAG_ALL_TASKS])->flush();
        return $result;
    }
    public function updateByModel(Task $task, array $data): Task
    {
        $result = $this->taskRepository->updateByModel($task, $data);
        $this->cache->tags([TaskCacheKeys::TAG_ALL_TASKS])->flush();
        return $result;
    }
    public function delete(int $id): bool
    {
        $result = $this->taskRepository->delete($id);
        $this->cache->tags([TaskCacheKeys::TAG_ALL_TASKS])->flush();
        return $result;
    }
}