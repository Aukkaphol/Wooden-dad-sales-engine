<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Database\Factories\MediaPipelineRunFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MediaPipelineRun extends Model
{
    /** @use HasFactory<MediaPipelineRunFactory> */
    use HasFactory, HasUuid, SoftDeletes;

    public const STAGE_ASSETS = 'asset_library';
    public const STAGE_PROMPTS = 'prompt_library';
    public const STAGE_CONTENT = 'generated_content';
    public const STAGE_APPROVAL = 'approval';
    public const STAGE_QUEUE = 'publishing_queue';
    public const STAGE_ANALYTICS = 'analytics_lite';
    public const STAGE_INSIGHTS = 'ai_insights';

    public const STAGES = [
        self::STAGE_ASSETS,
        self::STAGE_PROMPTS,
        self::STAGE_CONTENT,
        self::STAGE_APPROVAL,
        self::STAGE_QUEUE,
        self::STAGE_ANALYTICS,
        self::STAGE_INSIGHTS,
    ];

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING_APPROVAL = 'pending_approval';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_REVISION_REQUESTED = 'revision_requested';
    public const STATUS_QUEUED = 'queued';
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_INSIGHT_CREATED = 'insight_created';

    protected $fillable = [
        'workspace_id',
        'brand_id',
        'created_by',
        'asset_ids',
        'prompt_template_id',
        'prompt_version',
        'generated_content_id',
        'publishing_queue_item_id',
        'analytics_record_id',
        'ai_insight_id',
        'current_stage',
        'status',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'asset_ids' => 'array',
            'prompt_version' => 'integer',
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function promptTemplate(): BelongsTo
    {
        return $this->belongsTo(PromptTemplate::class);
    }

    public function generatedContent(): BelongsTo
    {
        return $this->belongsTo(GeneratedContent::class);
    }

    public function publishingQueueItem(): BelongsTo
    {
        return $this->belongsTo(PublishingQueueItem::class);
    }

    public function analyticsRecord(): BelongsTo
    {
        return $this->belongsTo(AnalyticsRecord::class);
    }

    public function aiInsight(): BelongsTo
    {
        return $this->belongsTo(AiInsight::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(MediaPipelineHistory::class);
    }
}
