<?php

namespace App\Domain\Task\Repositories;

use App\Domain\Task\Interfaces\Repositories\TaskRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Domain\Task\Models\Task;

class TaskRepository implements TaskRepositoryInterface
{

    /**
     * Hàm __construct() dùng để khởi tạo thuộc tính $task tương tự như cách ghi dưới đây:
     * private readonly Task $task;
     * public function __construct(Task $task)
     * {
     * $this->task = $task;
     * }
     */
    public function __construct(private readonly Task $model) {}

    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['user', 'assignee'])->latest()->paginate($perPage);
    }

    public function getByUserPaginated(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['user', 'assignee'])->where('user_id', $userId)->latest()->paginate($perPage);
    }

    public function findOrFail(int $id): Task
    {
        return $this->model->with(['user', 'assignee'])->findOrFail($id);
    }

    public function create(array $data): Task
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        // $task = $this->model->findOrFail($id);
        // $task->update($data);
        // return $task;

        return $this->model->findOrFail($id)->update($data);
    }

    public function updateByModel(Task $task, array $data): Task
    {
        $task->update($data);
        return $task;
    }

    public function delete(int $id): bool
    {
        return $this->model->findOrFail($id)->delete();
    }
}
