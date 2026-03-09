<?php

namespace App\Domain\Task\Interfaces\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Domain\Task\Models\Task;

interface TaskRepositoryInterface
{
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator;

    public function getByUserPaginated(int $userId, int $perPage = 15): LengthAwarePaginator;

    public function findOrFail(int $id): mixed;

    public function create(array $data): mixed;

    public function update(int $id, array $data): bool;

    public function updateByModel(Task $task, array $data): Task;

    public function delete(int $id): mixed;
}
