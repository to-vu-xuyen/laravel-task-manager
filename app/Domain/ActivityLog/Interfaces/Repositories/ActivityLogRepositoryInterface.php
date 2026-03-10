<?php

namespace App\Domain\ActivityLog\Interfaces\Repositories;

use App\Domain\ActivityLog\Models\ActivityLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ActivityLogRepositoryInterface
{
    public function create(array $data): ActivityLog;
    public function getByTarget(string $targetType, int $targetId, int $perPage = 15): LengthAwarePaginator;
    public function getByUser(int $userId, int $perPage = 15): LengthAwarePaginator;
    public function getByAction(string $action, int $perPage = 15): LengthAwarePaginator;
}
