<?php

namespace App\Http\Admin\Controllers;

use App\Domain\Task\Interfaces\Services\TaskServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Admin\Requests\StoreTaskRequest;
use App\Http\Admin\Requests\UpdateTaskRequest;
use App\Domain\Task\Models\Task;

class TaskController extends Controller
{
    protected TaskServiceInterface $taskService;

    public function __construct(TaskServiceInterface $taskService)
    {
        $this->taskService = $taskService;
    }

    public function index()
    {
        $tasks = $this->taskService->getAllPaginated();
        return view('admin.tasks.index', compact('tasks'));
    }

    public function create()
    {
        return view('admin.tasks.create');
    }

    public function store(StoreTaskRequest $request)
    {
        $this->taskService->create($request->validated());
        return redirect()->route('admin.tasks.index')->with('success', 'Task created successfully');
    }

    public function show(int $id)
    {
        $task = $this->taskService->findOrFail($id);
        return view('admin.tasks.show', compact('task'));
    }

    // public function edit(Task $task)
    // {
    //     return view('admin.tasks.edit', compact('task'));
    // }

    // public function update(UpdateTaskRequest $request, Task $task)
    // {
    //     $this->taskService->update($task->id, $request->validated());
    //     return redirect()->route('admin.tasks.index')->with('success', 'Task updated successfully');
    // }

    // public function destroy(Task $task)
    // {
    //     $this->taskService->delete($task->id);
    //     return redirect()->route('admin.tasks.index')->with('success', 'Task deleted successfully');
    // }
}
