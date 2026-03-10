<?php


namespace App\Domain\ActivityLog\Services;

use App\Domain\ActivityLog\Interfaces\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ActivityLog\Interfaces\Services\ActivityLogServiceInterface;
use App\Domain\ActivityLog\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ActivityLogService implements ActivityLogServiceInterface
{
    public function __construct(private readonly ActivityLogRepositoryInterface $repository) {}

    public function log(string $action, ?Model $target = null, array $meta = []): ActivityLog
    {
        return $this->repository->create([
            'user_id'     => Auth::id(),
            'action'      => $action,
            'target_type' => $target ? $target->getMorphClass() : null,
            'target_id'   => $target?->getKey(),
            'meta'        => !empty($meta) ? $meta : null,
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
        ]);
    }


    public function logModelEvent(Model $model, string $action, array $meta = []): ActivityLog
    {
        return $this->repository->create([
            'user_id'     => Auth::id(),
            'action'      => $action,
            'target_type' => $model->getMorphClass(),
            'target_id'   => $model->getKey(),
            'meta'        => !empty($meta) ? $meta : null,
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
        ]);
    }


    public function logRequest(Request $request, Response $response): ActivityLog
    {
        return $this->repository->create([
            'user_id'     => Auth::id(),
            'action'      => 'api_request',
            'target_type' => null,
            'target_id'   => null,
            'meta'        => [
                'method'      => $request->method(),
                'url'         => $request->fullUrl(),
                'route'       => $request->route()?->getName(),
                'status_code' => $response->getStatusCode(),
                'duration_ms' => round((microtime(true) - LARAVEL_START) * 1000),
            ],
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
        ]);
    }
}
