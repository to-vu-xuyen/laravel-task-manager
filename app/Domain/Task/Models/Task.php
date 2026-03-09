<?php

namespace App\Domain\Task\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Domain\Task\Models\TaskAttachment;
use App\Domain\Task\Enums\TaskStatus;

class Task extends Model
{
    use SoftDeletes; // Sử dụng trait SoftDeletes để có thể sử dụng phương thức delete() và forceDelete()

    /**
     * Những field được phép gán giá trị thông qua phương thức create() và update()
     * Nếu không khai báo fillable thì mặc định tất cả các field đều được phép gán giá trị
     * Khai báo fillable thì chỉ các field được khai báo mới được phép gán giá trị
     */
    protected $fillable = [
        'user_id',
        'assignee_id',
        'title',
        'description',
        'content',
        'status',
        'due_date',
    ];

    /**
     * Tự động chuyển 1 field string từ DB thành 1 data type khác
     */
    protected function casts(): array
    {
        return [
            'status' => TaskStatus::class,
            'due_at' => 'datetime',
        ];
    }

    /**
     * Thuộc về 1 user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Thuộc về 1 user
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    /**
     * Có nhiều attachments
     */
    public function attachments(): HasMany
    {
        // thêm chaperone() sau hasMany để query 1 lần thay vì N+1 lần
        return $this->hasMany(TaskAttachment::class);
    }

    /**
     * Scope dùng để query task của 1 user
     * 
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeWithStatus($query, TaskStatus $status)
    {
        return $query->where('status', $status);
    }
}
