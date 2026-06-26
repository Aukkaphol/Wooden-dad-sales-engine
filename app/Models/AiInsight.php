<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Database\Factories\AiInsightFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AiInsight extends Model
{
    /** @use HasFactory<AiInsightFactory> */
    use HasFactory, HasUuid, SoftDeletes;

    public const TYPE_PERFORMANCE_SUMMARY = 'performance_summary';
    public const TYPE_AUDIENCE_INSIGHT = 'audience_insight';
    public const TYPE_HOOK_IMPROVEMENT = 'hook_improvement';
    public const TYPE_CAPTION_IMPROVEMENT = 'caption_improvement';
    public const TYPE_CREATIVE_IMPROVEMENT = 'creative_improvement';
    public const TYPE_POSTING_TIME_RECOMMENDATION = 'posting_time_recommendation';
    public const TYPE_CAMPAIGN_RECOMMENDATION = 'campaign_recommendation';

    public const TYPES = [
        self::TYPE_PERFORMANCE_SUMMARY,
        self::TYPE_AUDIENCE_INSIGHT,
        self::TYPE_HOOK_IMPROVEMENT,
        self::TYPE_CAPTION_IMPROVEMENT,
        self::TYPE_CREATIVE_IMPROVEMENT,
        self::TYPE_POSTING_TIME_RECOMMENDATION,
        self::TYPE_CAMPAIGN_RECOMMENDATION,
    ];

    public const STATUS_NEW = 'new';
    public const STATUS_REVIEWED = 'reviewed';
    public const STATUS_APPLIED = 'applied';
    public const STATUS_IGNORED = 'ignored';

    public const STATUSES = [
        self::STATUS_NEW,
        self::STATUS_REVIEWED,
        self::STATUS_APPLIED,
        self::STATUS_IGNORED,
    ];

    public const PRIORITY_LOW = 'low';
    public const PRIORITY_MEDIUM = 'medium';
    public const PRIORITY_HIGH = 'high';

    public const PRIORITIES = [
        self::PRIORITY_LOW,
        self::PRIORITY_MEDIUM,
        self::PRIORITY_HIGH,
    ];

    protected $fillable = [
        'workspace_id',
        'brand_id',
        'generated_content_id',
        'analytics_record_id',
        'created_by',
        'insight_type',
        'title',
        'summary',
        'recommendation',
        'priority',
        'status',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
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

    public function generatedContent(): BelongsTo
    {
        return $this->belongsTo(GeneratedContent::class);
    }

    public function analyticsRecord(): BelongsTo
    {
        return $this->belongsTo(AnalyticsRecord::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
