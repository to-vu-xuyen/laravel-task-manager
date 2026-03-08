<?php

namespace App\Domain\Task\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Domain\Task\Models\TaskAttachment;
use App\Domain\Task\Enums\TaskStatus;

class Task extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'assignee_id',
        'title',
        'description',
        'content',
        'status',
        'due_date',
    ];

    protected function casts(): array
    {
        return [
            'status' => TaskStatus::class,
            'due_at' => 'datetime',
        ];
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class);
    }
}
