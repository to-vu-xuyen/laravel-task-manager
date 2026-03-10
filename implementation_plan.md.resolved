# Task Cache Implementation — Decorator Pattern + Cache Tags

## Background

Hiện tại project Task Manager chưa có cache layer. Mục tiêu:
1. **Cache Task data** để tăng performance
2. **Xóa tất cả cache Task** cùng lúc (flush all)
3. **Xóa cache cụ thể** — ví dụ: xóa tất cả cache Task của 1 user cụ thể
4. Tuân thủ **SOLID** và **best practices** của Laravel

## Architecture Decision

### Tại sao chọn Decorator Pattern?

```mermaid
graph LR
    Controller --> TaskService
    TaskService --> |"Interface"| CachingTaskRepository
    CachingTaskRepository --> |"Decorates"| TaskRepository
    TaskRepository --> Database
    CachingTaskRepository --> Cache["Laravel Cache (Tags)"]
```

| Principle | Giải thích |
|-----------|-----------|
| **S** — Single Responsibility | [TaskRepository](file:///c:/wamp64/www/l-task-manager/app/Domain/Task/Repositories/TaskRepository.php#9-62) chỉ query DB, `CachingTaskRepository` chỉ quản lý cache |
| **O** — Open/Closed | Không sửa [TaskRepository](file:///c:/wamp64/www/l-task-manager/app/Domain/Task/Repositories/TaskRepository.php#9-62) gốc, chỉ wrap nó |
| **L** — Liskov Substitution | `CachingTaskRepository` implement cùng [TaskRepositoryInterface](file:///c:/wamp64/www/l-task-manager/app/Domain/Task/Interfaces/Repositories/TaskRepositoryInterface.php#8-24) |
| **I** — Interface Segregation | Dùng chung interface, không tạo interface thừa |
| **D** — Dependency Inversion | Service depend vào [TaskRepositoryInterface](file:///c:/wamp64/www/l-task-manager/app/Domain/Task/Interfaces/Repositories/TaskRepositoryInterface.php#8-24), không biết concrete class |

### Tại sao chọn Cache Tags?

Cache Tags cho phép tổ chức cache theo nhóm và **xóa hàng loạt** cực kỳ dễ:

```php
// Lưu cache với nhiều tags
Cache::tags(['tasks', 'tasks:user:5'])->put('task:42', $task);

// Xóa TẤT CẢ cache Task
Cache::tags(['tasks'])->flush();

// Xóa chỉ cache Task của user #5
Cache::tags(['tasks:user:5'])->flush();
```

> [!IMPORTANT]
> Cache Tags **chỉ hỗ trợ** driver `redis` hoặc `memcached`. Driver `database` hiện tại **không hỗ trợ tags**. Cần đổi `CACHE_STORE=redis` trong `.env`.

### Cache Key Strategy

| Cache Key | Tags | Mô tả |
|-----------|------|-------|
| `tasks:all:page:{page}:per:{perPage}` | `['tasks']` | Danh sách tất cả tasks |
| `tasks:user:{userId}:page:{page}:per:{perPage}` | `['tasks', 'tasks:user:{userId}']` | Tasks của 1 user |
| `tasks:detail:{id}` | `['tasks', 'tasks:user:{userId}', 'tasks:item:{id}']` | Chi tiết 1 task |

---

## Proposed Changes

### Cache Infrastructure

#### [NEW] [TaskCacheKeys.php](file:///c:/wamp64/www/l-task-manager/app/Domain/Task/Cache/TaskCacheKeys.php)

Centralized cache key & tag management. Tất cả key và tag được định nghĩa tại đây, tránh magic strings rải rác.

```php
<?php

namespace App\Domain\Task\Cache;

class TaskCacheKeys
{
    public const TAG_ALL_TASKS = 'tasks';
    public const DEFAULT_TTL = 3600; // 1 hour

    public static function tagForUser(int $userId): string
    {
        return 'tasks:user:' . $userId;
    }

    public static function tagForItem(int $taskId): string
    {
        return 'tasks:item:' . $taskId;
    }

    public static function allTasksPaginated(int $page, int $perPage): string
    {
        return 'tasks:all:page:' . $page . ':per:' . $perPage;
    }

    public static function userTasksPaginated(int $userId, int $page, int $perPage): string
    {
        return 'tasks:user:' . $userId . ':page:' . $page . ':per:' . $perPage;
    }

    public static function taskDetail(int $taskId): string
    {
        return 'tasks:detail:' . $taskId;
    }
}
```

---

### Repository Layer (Decorator)

#### [NEW] [CachingTaskRepository.php](file:///c:/wamp64/www/l-task-manager/app/Domain/Task/Repositories/CachingTaskRepository.php)

Decorator wrapping [TaskRepository](file:///c:/wamp64/www/l-task-manager/app/Domain/Task/Repositories/TaskRepository.php#9-62). Implements [TaskRepositoryInterface](file:///c:/wamp64/www/l-task-manager/app/Domain/Task/Interfaces/Repositories/TaskRepositoryInterface.php#8-24):

- **Read methods** ([getAllPaginated](file:///c:/wamp64/www/l-task-manager/app/Domain/Task/Repositories/TaskRepository.php#22-26), [getByUserPaginated](file:///c:/wamp64/www/l-task-manager/app/Domain/Task/Interfaces/Services/TaskServiceInterface.php#12-13), [findOrFail](file:///c:/wamp64/www/l-task-manager/app/Domain/Task/Interfaces/Repositories/TaskRepositoryInterface.php#14-15)): check cache → miss → query DB → store cache
- **Write methods** ([create](file:///c:/wamp64/www/l-task-manager/app/Domain/Task/Interfaces/Repositories/TaskRepositoryInterface.php#16-17), [update](file:///c:/wamp64/www/l-task-manager/app/Domain/Task/Services/TaskService.php#49-68), [updateByModel](file:///c:/wamp64/www/l-task-manager/app/Domain/Task/Repositories/TaskRepository.php#51-56), [delete](file:///c:/wamp64/www/l-task-manager/app/Domain/Task/Interfaces/Services/TaskServiceInterface.php#20-21)): delegate to [TaskRepository](file:///c:/wamp64/www/l-task-manager/app/Domain/Task/Repositories/TaskRepository.php#9-62) → **invalidate related cache tags**

```php
<?php

namespace App\Domain\Task\Repositories;

use App\Domain\Task\Cache\TaskCacheKeys;
use App\Domain\Task\Interfaces\Repositories\TaskRepositoryInterface;
use App\Domain\Task\Models\Task;
use Illuminate\Cache\CacheManager;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CachingTaskRepository implements TaskRepositoryInterface
{
    public function __construct(
        private readonly TaskRepositoryInterface $inner,
        private readonly CacheManager $cache,
    ) {}

    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        $page = request()->input('page', 1);
        $key = TaskCacheKeys::allTasksPaginated((int) $page, $perPage);

        return $this->cache
            ->tags([TaskCacheKeys::TAG_ALL_TASKS])
            ->remember($key, TaskCacheKeys::DEFAULT_TTL, function () use ($perPage) {
                return $this->inner->getAllPaginated($perPage);
            });
    }

    public function getByUserPaginated(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        $page = request()->input('page', 1);
        $key = TaskCacheKeys::userTasksPaginated($userId, (int) $page, $perPage);

        return $this->cache
            ->tags([TaskCacheKeys::TAG_ALL_TASKS, TaskCacheKeys::tagForUser($userId)])
            ->remember($key, TaskCacheKeys::DEFAULT_TTL, function () use ($userId, $perPage) {
                return $this->inner->getByUserPaginated($userId, $perPage);
            });
    }

    public function findOrFail(int $id): Task
    {
        $key = TaskCacheKeys::taskDetail($id);

        return $this->cache
            ->tags([TaskCacheKeys::TAG_ALL_TASKS, TaskCacheKeys::tagForItem($id)])
            ->remember($key, TaskCacheKeys::DEFAULT_TTL, function () use ($id) {
                return $this->inner->findOrFail($id);
            });
    }

    // --- Write methods: delegate rồi invalidate cache ---

    public function create(array $data): Task
    {
        $task = $this->inner->create($data);

        // Flush danh sách (vì danh sách đã thay đổi)
        $this->cache->tags([TaskCacheKeys::TAG_ALL_TASKS])->flush();

        return $task;
    }

    public function update(int $id, array $data): bool
    {
        $result = $this->inner->update($id, $data);

        // Flush cache liên quan tới task cụ thể này
        $this->cache->tags([TaskCacheKeys::TAG_ALL_TASKS])->flush();

        return $result;
    }

    public function updateByModel(Task $task, array $data): Task
    {
        $result = $this->inner->updateByModel($task, $data);

        $this->cache->tags([TaskCacheKeys::TAG_ALL_TASKS])->flush();

        return $result;
    }

    public function delete(int $id): bool
    {
        $result = $this->inner->delete($id);

        $this->cache->tags([TaskCacheKeys::TAG_ALL_TASKS])->flush();

        return $result;
    }
}
```

---

### Cache Service (Manual Control)

#### [NEW] [TaskCacheServiceInterface.php](file:///c:/wamp64/www/l-task-manager/app/Domain/Task/Interfaces/Services/TaskCacheServiceInterface.php)

```php
<?php

namespace App\Domain\Task\Interfaces\Services;

interface TaskCacheServiceInterface
{
    public function flushAll(): void;              // Xóa TẤT CẢ cache Task
    public function flushForUser(int $userId): void; // Xóa cache Task của user
    public function flushForTask(int $taskId): void; // Xóa cache 1 task cụ thể
}
```

#### [NEW] [TaskCacheService.php](file:///c:/wamp64/www/l-task-manager/app/Domain/Task/Services/TaskCacheService.php)

```php
<?php

namespace App\Domain\Task\Services;

use App\Domain\Task\Cache\TaskCacheKeys;
use App\Domain\Task\Interfaces\Services\TaskCacheServiceInterface;
use Illuminate\Cache\CacheManager;

class TaskCacheService implements TaskCacheServiceInterface
{
    public function __construct(private readonly CacheManager $cache) {}

    public function flushAll(): void
    {
        $this->cache->tags([TaskCacheKeys::TAG_ALL_TASKS])->flush();
    }

    public function flushForUser(int $userId): void
    {
        $this->cache->tags([TaskCacheKeys::tagForUser($userId)])->flush();
    }

    public function flushForTask(int $taskId): void
    {
        $this->cache->tags([TaskCacheKeys::tagForItem($taskId)])->flush();
    }
}
```

---

### Event-Driven Cache Invalidation

#### [NEW] [TaskCacheEventSubscriber.php](file:///c:/wamp64/www/l-task-manager/app/Domain/Task/Listeners/TaskCacheEventSubscriber.php)

Tự động xóa cache khi có event [TaskCreated](file:///c:/wamp64/www/l-task-manager/app/Domain/Task/Events/TaskCreated.php#9-17) hoặc [TaskStatusChanged](file:///c:/wamp64/www/l-task-manager/app/Domain/Task/Events/TaskStatusChanged.php#9-15):

```php
<?php

namespace App\Domain\Task\Listeners;

use App\Domain\Task\Cache\TaskCacheKeys;
use App\Domain\Task\Events\TaskCreated;
use App\Domain\Task\Events\TaskStatusChanged;
use Illuminate\Cache\CacheManager;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Events\Attributes\AsEventListener;

class TaskCacheEventSubscriber
{
    public function __construct(private readonly CacheManager $cache) {}

    public function handleTaskCreated(TaskCreated $event): void
    {
        // Task mới tạo → flush danh sách để hiển thị task mới
        $this->cache->tags([TaskCacheKeys::TAG_ALL_TASKS])->flush();
    }

    public function handleTaskStatusChanged(TaskStatusChanged $event): void
    {
        // Status thay đổi → flush cache task cụ thể + danh sách
        $this->cache->tags([
            TaskCacheKeys::TAG_ALL_TASKS,
        ])->flush();
    }

    /**
     * Đăng ký các event listeners.
     *
     * @return array<string, string>
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            TaskCreated::class => 'handleTaskCreated',
            TaskStatusChanged::class => 'handleTaskStatusChanged',
        ];
    }
}
```

> [!NOTE]
> Subscriber sử dụng method `subscribe()` để đăng ký nhiều events trong 1 class. Cache sẽ bị invalidate khi event dispatch. Vì [TaskService](file:///c:/wamp64/www/l-task-manager/app/Domain/Task/Services/TaskService.php#13-74) đã dispatch events *sau* `DB::commit()`, data consistency được đảm bảo.



---

### DI Binding

#### [MODIFY] [AppServiceProvider.php](file:///c:/wamp64/www/l-task-manager/app/Providers/AppServiceProvider.php)

Thay đổi binding [TaskRepositoryInterface](file:///c:/wamp64/www/l-task-manager/app/Domain/Task/Interfaces/Repositories/TaskRepositoryInterface.php#8-24):

```diff
 // Task Domain
 $this->app->bind(TaskServiceInterface::class, TaskService::class);
-$this->app->bind(TaskRepositoryInterface::class, TaskRepository::class);
+$this->app->bind(TaskRepositoryInterface::class, function ($app) {
+    return new CachingTaskRepository(
+        inner: new TaskRepository($app->make(Task::class)),
+        cache: $app->make(CacheManager::class),
+    );
+});
+
+// Task Cache Service
+$this->app->bind(TaskCacheServiceInterface::class, TaskCacheService::class);
```

Thêm đăng ký Event Subscriber:

```diff
 public function boot(): void
 {
-    //
+    Event::subscribe(TaskCacheEventSubscriber::class);
 }
```

---

## File Summary

| # | Action | File | Purpose |
|---|--------|------|---------|
| 1 | NEW | [Cache/TaskCacheKeys.php](file:///c:/wamp64/www/l-task-manager/app/Domain/Task/Cache/TaskCacheKeys.php) | Cache key & tag constants |
| 2 | NEW | `Repositories/CachingTaskRepository.php` | Decorator pattern |
| 3 | NEW | `Interfaces/Services/TaskCacheServiceInterface.php` | Cache service interface |
| 4 | NEW | `Services/TaskCacheService.php` | Manual cache flush API |
| 5 | NEW | `Listeners/TaskCacheEventSubscriber.php` | Event-driven invalidation |
| 6 | MODIFY | [Providers/AppServiceProvider.php](file:///c:/wamp64/www/l-task-manager/app/Providers/AppServiceProvider.php) | DI bindings + subscriber |

---

## Verification Plan

### Automated Tests

Chạy PHPUnit test để đảm bảo không có regression:

```bash
cd c:\wamp64\www\l-task-manager
php artisan test
```

### Manual Verification

> [!IMPORTANT]
> Yêu cầu: Cần cài Redis và set `CACHE_STORE=redis` trong `.env` trước khi test. Nếu chưa có Redis, có thể test với `CACHE_STORE=array` (nhưng tags sẽ chỉ hoạt động trong 1 request, không persist).

1. **Test cache hit/miss**: Gọi API `GET /admin/tasks` 2 lần → lần 2 phải nhanh hơn (cache hit)
2. **Test invalidation on create**: Tạo task mới → gọi lại danh sách → phải thấy task mới (cache đã bị flush)
3. **Test flush all**: Gọi `TaskCacheService::flushAll()` → kiểm tra cache rỗng
4. **Test flush by user**: Gọi `TaskCacheService::flushForUser($userId)` → cache user đó bị xóa, cache user khác vẫn còn

Nếu bạn muốn test nhanh qua Tinker:

```bash
php artisan tinker
>>> app(\App\Domain\Task\Interfaces\Services\TaskCacheServiceInterface::class)->flushAll();
>>> // Kiểm tra cache đã bị xóa
```
