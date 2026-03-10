<?php

namespace App\Domain\ActivityLog\Interfaces\Services;


use App\Domain\ActivityLog\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;



interface ActivityLogServiceInterface
{

    /**
     * Log action thủ công - gọi từ Controller/Job.
     *
     * Ví dụ:
     *   $this->activityLog->log('task.exported', $task, ['format' => 'csv']);
     *   $this->activityLog->log('user.login');
     *   $this->activityLog->log('report.generated', null, ['type' => 'monthly']);
     */
    public function log(string $action, ?Model $target = null, array $meta = []): ActivityLog;

    /**
     * Log Eloquent model event - gọi tự động từ LogsActivity Trait.
     * Trait sẽ truyền action dạng "{modelName}.created", "{modelName}.updated"...
     */
    public function logModelEvent(Model $model, string $action, array $meta = []): ActivityLog;

    /**
     * Log HTTP request/response - gọi từ LogHttpRequest Middleware.
     */
    public function logRequest(Request $request, Response $response): ActivityLog;
}
