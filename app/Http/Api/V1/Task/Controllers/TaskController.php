<?php

namespace App\Http\Api\V1\Task\Controllers;


use App\Domain\Task\Interfaces\Services\TaskServiceInterface;
use App\Http\Api\V1\Task\Requests\StoreTaskRequest;
use App\Http\Api\V1\Task\Requests\UpdateTaskRequest;
use App\Http\Api\V1\Task\Resources\TaskResource;
use App\Http\Api\V1\Task\Resources\TaskCollection;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\JsonResponse;

class TaskController
{
    public function __construct(
        private readonly TaskServiceInterface $taskService
    ) {}


    public function index(): TaskCollection
    {
        $tasks = $this->taskService->getAllPaginated();
        return new TaskCollection($tasks);
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = $this->taskService->create($request->validated());
        return TaskResource::make($task)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
    public function show(int $id): TaskResource
    {
        $task = $this->taskService->findOrFail($id);
        return new TaskResource($task);
    }

    public function update(UpdateTaskRequest $request, int $id): TaskResource
    {
        $task = $this->taskService->update($id, $request->validated());
        return new TaskResource($task);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->taskService->delete($id);
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
