<?php

namespace App\Domain\ActivityLog\Repositories;

use App\Domain\ActivityLog\Interfaces\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ActivityLog\Models\ActivityLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ActivityLogRepository implements ActivityLogRepositoryInterface
{
    public function __construct(private readonly ActivityLog $model) {}

    public function create(array $data): ActivityLog
    {
        return $this->model->create($data);
    }

    public function getByTarget(string $targetType, int $targetId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->where('target_type', $targetType)
            ->where('target_id', $targetId)
            ->latest('created_at')
            ->paginate($perPage);
    }

    public function getByUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->where('user_id', $userId)
            ->latest('created_at')
            ->paginate($perPage);
    }

    public function getByAction(string $action, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->where('action', $action)
            ->latest('created_at')
            ->paginate($perPage);
    }
}
