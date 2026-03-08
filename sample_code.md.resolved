# 📦 Code Mẫu — Kiến Trúc Admin/User (DDD + Clean Architecture)

Toàn bộ code bên dưới đã được viết sát với schema migrations thực tế của project `laravel-task-manager`.

> [!TIP]
> Copy từng file vào đúng đường dẫn tương ứng. Hoặc yêu cầu tôi scaffold tự động.

---

## 🔵 DOMAIN LAYER — Core Logic (Dùng chung)

---

### `app/Domain/Task/Enums/TaskStatus.php`
```php
<?php

namespace App\Domain\Task\Enums;

enum TaskStatus: string
{
    case Pending    = 'pending';
    case InProgress = 'in_progress';
    case Completed  = 'completed';
    case Cancelled  = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending    => 'Chờ xử lý',
            self::InProgress => 'Đang thực hiện',
            self::Completed  => 'Hoàn thành',
            self::Cancelled  => 'Đã hủy',
        };
    }
}
```

---

### [app/Domain/Task/Models/Task.php](file:///e:/ProgramFiles/wamp/www/laravel-task-manager/app/Domain/Task/Models/Task.php)
```php
<?php

namespace App\Domain\Task\Models;

use App\Domain\Task\Enums\TaskStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'assignee_id',
        'title',
        'description',
        'content',
        'status',
        'due_date',
    ];

    protected function casts(): array
    {
        return [
            'status'   => TaskStatus::class,
            'due_date' => 'datetime',
        ];
    }

    // ─── Relationships ───

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TaskAttachment::class);
    }

    // ─── Scopes ───

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeWithStatus($query, TaskStatus $status)
    {
        return $query->where('status', $status);
    }
}
```

---

### `app/Domain/Task/Models/TaskAttachment.php`
```php
<?php

namespace App\Domain\Task\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskAttachment extends Model
{
    protected $fillable = [
        'user_id',
        'task_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
```

---

### `app/Domain/Task/Contracts/TaskRepositoryInterface.php`
```php
<?php

namespace App\Domain\Task\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TaskRepositoryInterface
{
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator;

    public function getByUserPaginated(int $userId, int $perPage = 15): LengthAwarePaginator;

    public function findOrFail(int $id): mixed;

    public function create(array $data): mixed;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;
}
```

---

### `app/Domain/Task/Repositories/TaskRepository.php`
```php
<?php

namespace App\Domain\Task\Repositories;

use App\Domain\Task\Contracts\TaskRepositoryInterface;
use App\Domain\Task\Models\Task;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TaskRepository implements TaskRepositoryInterface
{
    public function __construct(
        private readonly Task $model,
    ) {}

    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with(['owner', 'assignee'])
            ->latest()
            ->paginate($perPage);
    }

    public function getByUserPaginated(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->forUser($userId)
            ->with('assignee')
            ->latest()
            ->paginate($perPage);
    }

    public function findOrFail(int $id): Task
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data): Task
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->model->findOrFail($id)->update($data);
    }

    public function delete(int $id): bool
    {
        return $this->model->findOrFail($id)->delete();
    }
}
```

---

### `app/Domain/Task/Contracts/TaskServiceInterface.php`
```php
<?php

namespace App\Domain\Task\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TaskServiceInterface
{
    /** Admin: lấy toàn bộ tasks */
    public function getAllTasks(int $perPage = 15): LengthAwarePaginator;

    /** User: chỉ lấy tasks của chính mình */
    public function getTasksForUser(int $userId, int $perPage = 15): LengthAwarePaginator;

    public function getTaskById(int $id): mixed;

    public function createTask(array $data): mixed;

    public function updateTask(int $id, array $data): bool;

    public function deleteTask(int $id): bool;
}
```

---

### `app/Domain/Task/Services/TaskService.php`
```php
<?php

namespace App\Domain\Task\Services;

use App\Domain\Task\Contracts\TaskRepositoryInterface;
use App\Domain\Task\Contracts\TaskServiceInterface;
use App\Domain\Task\Events\TaskCreated;
use App\Domain\Task\Events\TaskStatusChanged;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TaskService implements TaskServiceInterface
{
    public function __construct(
        private readonly TaskRepositoryInterface $repository,
    ) {}

    public function getAllTasks(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getAllPaginated($perPage);
    }

    public function getTasksForUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getByUserPaginated($userId, $perPage);
    }

    public function getTaskById(int $id): mixed
    {
        return $this->repository->findOrFail($id);
    }

    public function createTask(array $data): mixed
    {
        $task = $this->repository->create($data);

        // Phát sự kiện → ActivityLog Domain sẽ lắng nghe
        TaskCreated::dispatch($task);

        return $task;
    }

    public function updateTask(int $id, array $data): bool
    {
        $oldTask = $this->repository->findOrFail($id);
        $oldStatus = $oldTask->status;

        $result = $this->repository->update($id, $data);

        if (isset($data['status']) && $oldStatus->value !== $data['status']) {
            TaskStatusChanged::dispatch($oldTask->fresh(), $oldStatus->value);
        }

        return $result;
    }

    public function deleteTask(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
```

---

### `app/Domain/Task/Events/TaskCreated.php`
```php
<?php

namespace App\Domain\Task\Events;

use App\Domain\Task\Models\Task;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Task $task,
    ) {}
}
```

---

### `app/Domain/Task/Events/TaskStatusChanged.php`
```php
<?php

namespace App\Domain\Task\Events;

use App\Domain\Task\Models\Task;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Task $task,
        public readonly string $oldStatus,
    ) {}
}
```

---

## 🔵 DOMAIN — ActivityLog Module

---

### `app/Domain/ActivityLog/Models/ActivityLog.php`
```php
<?php

namespace App\Domain\ActivityLog\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    public $timestamps = false; // Chỉ có created_at, không có updated_at

    protected $fillable = [
        'user_id',
        'action',
        'target_type',
        'target_id',
        'meta',
        'ip_address',
        'user_agent',
        'error_message',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'meta'       => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function target(): MorphTo
    {
        return $this->morphTo();
    }
}
```

---

### `app/Domain/ActivityLog/Contracts/ActivityLogServiceInterface.php`
```php
<?php

namespace App\Domain\ActivityLog\Contracts;

interface ActivityLogServiceInterface
{
    public function log(
        int $userId,
        string $action,
        string $targetType,
        int $targetId,
        ?array $meta = null,
        ?string $ipAddress = null,
        ?string $userAgent = null,
    ): void;
}
```

---

### `app/Domain/ActivityLog/Services/ActivityLogService.php`
```php
<?php

namespace App\Domain\ActivityLog\Services;

use App\Domain\ActivityLog\Contracts\ActivityLogServiceInterface;
use App\Domain\ActivityLog\Models\ActivityLog;

class ActivityLogService implements ActivityLogServiceInterface
{
    public function log(
        int $userId,
        string $action,
        string $targetType,
        int $targetId,
        ?array $meta = null,
        ?string $ipAddress = null,
        ?string $userAgent = null,
    ): void {
        ActivityLog::create([
            'user_id'     => $userId,
            'action'      => $action,
            'target_type' => $targetType,
            'target_id'   => $targetId,
            'meta'        => $meta,
            'ip_address'  => $ipAddress,
            'user_agent'  => $userAgent,
            'created_at'  => now(),
        ]);
    }
}
```

---

### `app/Domain/ActivityLog/Listeners/LogTaskActivity.php`
```php
<?php

namespace App\Domain\ActivityLog\Listeners;

use App\Domain\ActivityLog\Contracts\ActivityLogServiceInterface;
use App\Domain\Task\Events\TaskCreated;
use App\Domain\Task\Events\TaskStatusChanged;

class LogTaskActivity
{
    public function __construct(
        private readonly ActivityLogServiceInterface $logService,
    ) {}

    /**
     * Handle TaskCreated hoặc TaskStatusChanged.
     * Laravel tự inject đúng event vào argument.
     */
    public function handle(TaskCreated|TaskStatusChanged $event): void
    {
        $task   = $event->task;
        $action = $event instanceof TaskCreated ? 'task_created' : 'task_status_changed';

        $meta = ['title' => $task->title];
        if ($event instanceof TaskStatusChanged) {
            $meta['old_status'] = $event->oldStatus;
            $meta['new_status'] = $task->status->value;
        }

        $this->logService->log(
            userId:     $task->user_id,
            action:     $action,
            targetType: 'task',
            targetId:   $task->id,
            meta:       $meta,
        );
    }
}
```

---

## 🟢 HTTP LAYER — Presentation (Tách Admin / Web)

---

### `app/Http/Requests/Admin/StoreTaskRequest.php`
```php
<?php

namespace App\Http\Requests\Admin;

use App\Domain\Task\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Middleware admin đã check rồi
    }

    public function rules(): array
    {
        return [
            'user_id'     => ['required', 'exists:users,id'],
            'assignee_id' => ['nullable', 'exists:users,id'],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'content'     => ['nullable', 'string'],
            'status'      => ['sometimes', Rule::enum(TaskStatus::class)],
            'due_date'    => ['nullable', 'date', 'after:now'],
        ];
    }
}
```

---

### `app/Http/Requests/Web/StoreTaskRequest.php`
```php
<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Chỉ cần đăng nhập (middleware auth đã check)
    }

    public function rules(): array
    {
        return [
            // User KHÔNG được chọn user_id (tự gán chính mình)
            // User KHÔNG được chọn status (mặc định pending)
            'assignee_id' => ['nullable', 'exists:users,id'],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'content'     => ['nullable', 'string'],
            'due_date'    => ['nullable', 'date', 'after:now'],
        ];
    }
}
```

---

### `app/Http/Controllers/Admin/TaskController.php`
```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTaskRequest;
use App\Domain\Task\Contracts\TaskServiceInterface;

class TaskController extends Controller
{
    public function __construct(
        private readonly TaskServiceInterface $taskService,
    ) {}

    public function index()
    {
        $tasks = $this->taskService->getAllTasks();

        return view('admin.tasks.index', compact('tasks'));
    }

    public function create()
    {
        return view('admin.tasks.create');
    }

    public function store(StoreTaskRequest $request)
    {
        $this->taskService->createTask($request->validated());

        return redirect()->route('admin.tasks.index')
            ->with('success', 'Task đã được tạo.');
    }

    public function show(int $id)
    {
        $task = $this->taskService->getTaskById($id);

        return view('admin.tasks.show', compact('task'));
    }
}
```

---

### `app/Http/Controllers/Web/TaskController.php`
```php
<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\StoreTaskRequest;
use App\Domain\Task\Contracts\TaskServiceInterface;

class TaskController extends Controller
{
    public function __construct(
        private readonly TaskServiceInterface $taskService,
    ) {}

    public function index()
    {
        // User chỉ thấy task của CHÍNH MÌNH
        $tasks = $this->taskService->getTasksForUser(auth()->id());

        return view('web.tasks.index', compact('tasks'));
    }

    public function create()
    {
        return view('web.tasks.create');
    }

    public function store(StoreTaskRequest $request)
    {
        // Tự gán user_id = chính user đang đăng nhập
        $data = array_merge($request->validated(), [
            'user_id' => auth()->id(),
            'status'  => 'pending',
        ]);

        $this->taskService->createTask($data);

        return redirect()->route('tasks.index')
            ->with('success', 'Task đã được tạo.');
    }

    public function show(int $id)
    {
        $task = $this->taskService->getTaskById($id);

        // Bảo vệ: user chỉ xem được task của mình
        abort_unless($task->user_id === auth()->id(), 403);

        return view('web.tasks.show', compact('task'));
    }
}
```

---

### `app/Http/Middleware/IsAdmin.php`
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Giả sử User model có thuộc tính 'role'
        if (auth()->check() && auth()->user()->role === 'admin') {
            return $next($request);
        }

        abort(403, 'Bạn không có quyền truy cập.');
    }
}
```

---

## ⚙️ SERVICE PROVIDERS

---

### `app/Providers/TaskServiceProvider.php`
```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Task\Contracts\TaskServiceInterface;
use App\Domain\Task\Contracts\TaskRepositoryInterface;
use App\Domain\Task\Services\TaskService;
use App\Domain\Task\Repositories\TaskRepository;

class TaskServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TaskRepositoryInterface::class, TaskRepository::class);
        $this->app->bind(TaskServiceInterface::class, TaskService::class);
    }
}
```

---

### `app/Providers/ActivityLogServiceProvider.php`
```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Domain\ActivityLog\Contracts\ActivityLogServiceInterface;
use App\Domain\ActivityLog\Services\ActivityLogService;
use App\Domain\Task\Events\TaskCreated;
use App\Domain\Task\Events\TaskStatusChanged;
use App\Domain\ActivityLog\Listeners\LogTaskActivity;

class ActivityLogServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ActivityLogServiceInterface::class, ActivityLogService::class);
    }

    public function boot(): void
    {
        Event::listen(TaskCreated::class, LogTaskActivity::class);
        Event::listen(TaskStatusChanged::class, LogTaskActivity::class);
    }
}
```

---

### [bootstrap/providers.php](file:///e:/ProgramFiles/wamp/www/laravel-task-manager/bootstrap/providers.php) (Đăng ký)
```php
<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\TaskServiceProvider::class,
    App\Providers\ActivityLogServiceProvider::class,
];
```

---

## 🌐 ROUTES

---

### [routes/web.php](file:///e:/ProgramFiles/wamp/www/laravel-task-manager/routes/web.php)
```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\TaskController;

Route::middleware(['auth'])->group(function () {
    Route::resource('tasks', TaskController::class);
});
```

---

### `routes/admin.php` (Tạo mới)
```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\TaskController;

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('tasks', TaskController::class);
    });
```

---

### [bootstrap/app.php](file:///e:/ProgramFiles/wamp/www/laravel-task-manager/bootstrap/app.php) (Đăng ký route admin + middleware)
```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\IsAdmin;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware(['web'])
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => IsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
```

---

## 📁 Bản đồ thư mục cuối cùng
```text
app/
├── Domain/
│   ├── Task/
│   │   ├── Contracts/
│   │   │   ├── TaskRepositoryInterface.php
│   │   │   └── TaskServiceInterface.php
│   │   ├── Enums/
│   │   │   └── TaskStatus.php
│   │   ├── Events/
│   │   │   ├── TaskCreated.php
│   │   │   └── TaskStatusChanged.php
│   │   ├── Models/
│   │   │   ├── Task.php
│   │   │   └── TaskAttachment.php
│   │   ├── Repositories/
│   │   │   └── TaskRepository.php
│   │   └── Services/
│   │       └── TaskService.php
│   │
│   └── ActivityLog/
│       ├── Contracts/
│       │   └── ActivityLogServiceInterface.php
│       ├── Listeners/
│       │   └── LogTaskActivity.php
│       ├── Models/
│       │   └── ActivityLog.php
│       └── Services/
│           └── ActivityLogService.php
│
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   └── TaskController.php
│   │   └── Web/
│   │       └── TaskController.php
│   ├── Middleware/
│   │   └── IsAdmin.php
│   └── Requests/
│       ├── Admin/
│       │   └── StoreTaskRequest.php
│       └── Web/
│           └── StoreTaskRequest.php
│
├── Models/
│   └── User.php                     ← Cross-cutting, giữ nguyên
│
└── Providers/
    ├── AppServiceProvider.php
    ├── TaskServiceProvider.php
    └── ActivityLogServiceProvider.php
```
