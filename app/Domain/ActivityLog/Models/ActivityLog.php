<?php

namespace App\Domain\ActivityLog\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{

    protected $fillable = [
        'user_id',
        'action',
        'target_type',
        'target_id',
        'meta',
        'ip_address',
        'user_agent',
        'error_message',
    ];

    protected $casts = [
        'meta' => 'json',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * MorphTo nghĩa là trả về Model Object (khi tên namespace class được lưu ở field type)
     * và ID (lưu ở field id) sẽ ra object Model có ID đó
     * @return MorphTo<Model, ActivityLog>
     */
    public function target(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeByAction(Builder $query, string $action): Builder
    {
        return $query->where('action', $action);
    }
    public function scopeForTarget(Builder $query, Model $target): Builder
    {
        return $query
            ->where('target_type', $target->getMorphClass())
            ->where('target_id', $target->getKey());
    }
}
