<?php

namespace App\Domain\Task\Interfaces\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Domain\Task\Models\Task;

interface TaskServiceInterface
{
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator;

    public function getByUserPaginated(int $userId, int $perPage = 15): LengthAwarePaginator;

    public function findOrFail(int $id): Task;

    public function create(array $data): Task;

    public function update(int $id, array $data): Task;

    public function delete(int $id): bool;
}
