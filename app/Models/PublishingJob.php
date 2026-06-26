<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Database\Factories\PublishingJobFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PublishingJob extends Model
{
    /** @use HasFactory<PublishingJobFactory> */
    use HasFactory, HasUuid, SoftDeletes;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_QUEUED = 'queued';
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_FAILED = 'failed';
    public const STATUS_CANCELLED = 'cancelled';

    public const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_QUEUED,
        self::STATUS_SCHEDULED,
        self::STATUS_PROCESSING,
        self::STATUS_PUBLISHED,
        self::STATUS_FAILED,
        self::STATUS_CANCELLED,
    ];

    protected $fillable = [
        'workspace_id',
        'brand_id',
        'publishing_queue_item_id',
        'social_account_id',
        'created_by',
        'platform',
        'status',
        'scheduled_at',
        'started_at',
        'finished_at',
        'attempts',
        'failure_reason',
        'provider_post_id',
        'provider_response',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
            'attempts' => 'integer',
            'provider_response' => 'array',
            'metadata' => 'array',
        ];
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function queueItem(): BelongsTo
    {
        return $this->belongsTo(PublishingQueueItem::class, 'publishing_queue_item_id');
    }

    public function socialAccount(): BelongsTo
    {
        return $this->belongsTo(SocialAccount::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(PublishingLog::class);
    }
}
