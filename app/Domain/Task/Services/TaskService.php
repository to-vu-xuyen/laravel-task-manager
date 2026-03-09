<?php

namespace App\Domain\Task\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Domain\Task\Events\TaskStatusChanged;
use App\Domain\Task\Models\Task;
use App\Domain\Task\Interfaces\Repositories\TaskRepositoryInterface;

class TaskService
{
    public function __construct(private readonly TaskRepositoryInterface $repository) {}

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
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): Task
    {
        $model = $this->repository->findOrFail($id);
        $oldStatus = $model->status;

        $result = $this->repository->updateByModel($model, $data);

        if (isset($data['status']) && $oldStatus->value !== $data['status']) {
            TaskStatusChanged::dispatch($result, $oldStatus->value);
        }

        return $result;
    }

    public function delete(int $id): Task
    {
        return $this->repository->delete($id);
    }
}
