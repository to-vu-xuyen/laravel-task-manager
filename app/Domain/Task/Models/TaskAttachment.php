<?php

namespace App\Domain\Task\Models;

use Illuminate\Database\Eloquent\Model;

class TaskAttachment extends Model
{
    protected $fillable = [
        'task_id',
        'user_id',
        'file_name',
        'file_path',
        'file_size',
        'file_type',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
