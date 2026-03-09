<?php

namespace App\Domain\Task\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use App\Domain\Task\Models\Task;
use App\Domain\Task\Events\TaskCreated;
use App\Domain\Task\Events\TaskStatusChanged;
use App\Domain\Task\Interfaces\Services\TaskServiceInterface;
use App\Domain\Task\Interfaces\Repositories\TaskRepositoryInterface;

class TaskService implements TaskServiceInterface
{
    public function __construct(private readonly TaskRepositoryInterface $repository)
    {
    }

    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getAllPaginated($perPage);
    }

    public function getByUserPaginated(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getByUserPaginated($userId, $perPage);
    }

    public function findOrFail(int $id): Task
    {
        return $this->repository->findOrFail($id);
    }

    public function create(array $data): Task
    {
        DB::beginTransaction();

        try {
            $task = $this->repository->create($data);
            TaskCreated::dispatch($task);
            DB::commit();
            return $task;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(int $id, array $data): Task
    {
        DB::beginTransaction();
        try {
            $model = $this->repository->findOrFail($id);
            $oldStatus = $model->status;
            $result = $this->repository->updateByModel($model, $data);

            if (isset($data['status']) && $oldStatus->value !== $data['status']) {
                TaskStatusChanged::dispatch($result, $oldStatus->value);
            }

            DB::commit();
            return $result;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
