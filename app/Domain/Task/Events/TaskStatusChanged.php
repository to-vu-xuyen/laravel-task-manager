<?php

namespace App\Domain\Task\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Domain\Task\Models\Task;

class TaskStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Task $task, public readonly string $oldStatus) {}
}
