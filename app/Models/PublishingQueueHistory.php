<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Database\Factories\PublishingQueueHistoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PublishingQueueHistory extends Model
{
    /** @use HasFactory<PublishingQueueHistoryFactory> */
    use HasFactory, HasUuid, SoftDeletes;

    public const EVENT_SCHEDULED = 'scheduled';
    public const EVENT_CANCELLED = 'cancelled';
    public const EVENT_RETRIED = 'retried';
    public const EVENT_PROCESSING = 'processing';
    public const EVENT_PUBLISHED = 'published';
    public const EVENT_FAILED = 'failed';

    protected $fillable = [
        'publishing_queue_item_id',
        'actor_id',
        'event',
        'previous_status',
        'new_status',
        'comment',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function queueItem(): BelongsTo
    {
        return $this->belongsTo(PublishingQueueItem::class, 'publishing_queue_item_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
