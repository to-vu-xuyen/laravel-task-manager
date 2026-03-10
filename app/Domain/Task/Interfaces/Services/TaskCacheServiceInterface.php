<?php


namespace App\Domain\Task\Interfaces\Services;
interface TaskCacheServiceInterface
{
    /**
     * Xóa TẤT CẢ cache
     */
    public function flushAll(): void;

    /**
     * Xóa cache tất cả Task liên quan tới user cụ thể.
     */
    public function flushForUser(int $userId): void;

    /**
     * Xóa cache chi tiết 1 task cụ thể.
     */
    public function flushForTask(int $taskId): void;
}