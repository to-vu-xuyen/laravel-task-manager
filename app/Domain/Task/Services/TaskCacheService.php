<?php

namespace App\Domain\Task\Services;

use App\Domain\Task\Cache\TaskCacheKeys;
use App\Domain\Task\Interfaces\Services\TaskCacheServiceInterface;
use Illuminate\Cache\CacheManager;

class TaskCacheService implements TaskCacheServiceInterface
{
    public function __construct(
        private readonly CacheManager $cache,
    ) {
    }

    public function flushAll(): void
    {
        $this->cache->tags([TaskCacheKeys::TAG_ALL_TASKS])->flush();
    }

    /**
     * Xóa cache tất cả Task liên quan tới user cụ thể.
     *
     * Flush tag `tasks:user:{userId}` → chỉ xóa cache entries
     * được tag với user này (danh sách user + task details của user).
     * Cache của user khác không bị ảnh hưởng.
     */
    public function flushForUser(int $userId): void
    {
        $this->cache->tags([TaskCacheKeys::tagForUser($userId)])->flush();
    }

    /**
     * Xóa cache chi tiết 1 task cụ thể.
     *
     * Flush tag `tasks:item:{taskId}` → chỉ xóa cache entry
     * chi tiết của task này. Danh sách và cache task khác không bị ảnh hưởng.
     */
    public function flushForTask(int $taskId): void
    {
        $this->cache->tags([TaskCacheKeys::tagForItem($taskId)])->flush();
    }
}
