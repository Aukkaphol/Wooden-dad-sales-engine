<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Database\Factories\GeneratedContentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class GeneratedContent extends Model
{
    /** @use HasFactory<GeneratedContentFactory> */
    use HasFactory, HasUuid, SoftDeletes;

    public const TYPE_FACEBOOK_POST = 'facebook_post';
    public const TYPE_FACEBOOK_CAROUSEL = 'facebook_carousel';
    public const TYPE_FACEBOOK_REEL_SCRIPT = 'facebook_reel_script';
    public const TYPE_TIKTOK_SCRIPT = 'tiktok_script';
    public const TYPE_INSTAGRAM_CAPTION = 'instagram_caption';
    public const TYPE_LINE_OA_BROADCAST = 'line_oa_broadcast';
    public const TYPE_PRODUCT_DESCRIPTION = 'product_description';
    public const TYPE_SEO_ARTICLE = 'seo_article';
    public const TYPE_BLOG = 'blog';
    public const TYPE_ADVERTISEMENT_COPY = 'advertisement_copy';
    public const TYPE_IMAGE_PROMPT = 'image_prompt';
    public const TYPE_VIDEO_PROMPT = 'video_prompt';

    public const TYPES = [
        self::TYPE_FACEBOOK_POST,
        self::TYPE_FACEBOOK_CAROUSEL,
        self::TYPE_FACEBOOK_REEL_SCRIPT,
        self::TYPE_TIKTOK_SCRIPT,
        self::TYPE_INSTAGRAM_CAPTION,
        self::TYPE_LINE_OA_BROADCAST,
        self::TYPE_PRODUCT_DESCRIPTION,
        self::TYPE_SEO_ARTICLE,
        self::TYPE_BLOG,
        self::TYPE_ADVERTISEMENT_COPY,
        self::TYPE_IMAGE_PROMPT,
        self::TYPE_VIDEO_PROMPT,
    ];

    public const STATUS_DRAFT = 'draft';
    public const STATUS_IN_REVIEW = 'in_review';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_ARCHIVED = 'archived';

    public const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_IN_REVIEW,
        self::STATUS_APPROVED,
        self::STATUS_REJECTED,
        self::STATUS_SCHEDULED,
        self::STATUS_PUBLISHED,
        self::STATUS_ARCHIVED,
    ];

    protected $fillable = [
        'workspace_id',
        'brand_id',
        'prompt_template_id',
        'created_by',
        'title',
        'platform',
        'content_type',
        'prompt_snapshot',
        'variables',
        'generated_content',
        'status',
        'scheduled_at',
        'published_at',
        'reviewer_notes',
        'version',
        'tags',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'variables' => 'array',
            'tags' => 'array',
            'version' => 'integer',
            'scheduled_at' => 'datetime',
            'published_at' => 'datetime',
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

    public function promptTemplate(): BelongsTo
    {
        return $this->belongsTo(PromptTemplate::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assets(): BelongsToMany
    {
        return $this->belongsToMany(Asset::class, 'generated_content_assets')->withTimestamps();
    }

    public function versions(): HasMany
    {
        return $this->hasMany(GeneratedContentVersion::class);
    }

    public function approvalHistories(): HasMany
    {
        return $this->hasMany(ContentApprovalHistory::class);
    }

    public function publishingQueueItems(): HasMany
    {
        return $this->hasMany(PublishingQueueItem::class);
    }

    public function analyticsRecords(): HasMany
    {
        return $this->hasMany(AnalyticsRecord::class);
    }

    public function aiInsights(): HasMany
    {
        return $this->hasMany(AiInsight::class);
    }

    public function mediaPipelineRuns(): HasMany
    {
        return $this->hasMany(MediaPipelineRun::class);
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }
}
