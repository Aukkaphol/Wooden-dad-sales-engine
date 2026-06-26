<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Database\Factories\PublishingQueueItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PublishingQueueItem extends Model
{
    /** @use HasFactory<PublishingQueueItemFactory> */
    use HasFactory, HasUuid, SoftDeletes;

    public const STATUS_WAITING = 'waiting';
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_FAILED = 'failed';
    public const STATUS_CANCELLED = 'cancelled';

    public const STATUSES = [
        self::STATUS_WAITING,
        self::STATUS_SCHEDULED,
        self::STATUS_PROCESSING,
        self::STATUS_PUBLISHED,
        self::STATUS_FAILED,
        self::STATUS_CANCELLED,
    ];

    protected $fillable = [
        'workspace_id',
        'brand_id',
        'generated_content_id',
        'created_by',
        'platform',
        'status',
        'scheduled_at',
        'published_at',
        'retry_count',
        'failure_reason',
        'priority',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'published_at' => 'datetime',
            'retry_count' => 'integer',
            'priority' => 'integer',
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

    public function generatedContent(): BelongsTo
    {
        return $this->belongsTo(GeneratedContent::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(PublishingQueueHistory::class);
    }

    public function analyticsRecords(): HasMany
    {
        return $this->hasMany(AnalyticsRecord::class);
    }

    public function publishingJobs(): HasMany
    {
        return $this->hasMany(PublishingJob::class);
    }
}
