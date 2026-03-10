<?php

namespace App\Http\Middleware;

use App\Domain\ActivityLog\Interfaces\Services\ActivityLogServiceInterface;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LogHttpRequest
{
    public function __construct(private readonly ActivityLogServiceInterface $activityLogService) {}

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request); // Thực hiện chạy code bên trong
        $this->activityLogService->logRequest($request, $response); // Ghi log
        return $response;
    }
}
