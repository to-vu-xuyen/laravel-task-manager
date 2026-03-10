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
