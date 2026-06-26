<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Database\Factories\AnalyticsRecordFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnalyticsRecord extends Model
{
    /** @use HasFactory<AnalyticsRecordFactory> */
    use HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'workspace_id',
        'brand_id',
        'generated_content_id',
        'publishing_queue_item_id',
        'created_by',
        'platform',
        'posted_at',
        'captured_at',
        'views',
        'reach',
        'impressions',
        'likes',
        'comments',
        'shares',
        'saves',
        'follows_gained',
        'link_clicks',
        'ctr',
        'engagement_rate',
        'estimated_revenue',
        'cost',
        'roas',
        'notes',
        'audience_breakdown',
        'metadata',
        'score',
        'score_reason',
        'recommendation',
    ];

    protected function casts(): array
    {
        return [
            'posted_at' => 'datetime',
            'captured_at' => 'datetime',
            'audience_breakdown' => 'array',
            'metadata' => 'array',
            'views' => 'integer',
            'reach' => 'integer',
            'impressions' => 'integer',
            'likes' => 'integer',
            'comments' => 'integer',
            'shares' => 'integer',
            'saves' => 'integer',
            'follows_gained' => 'integer',
            'link_clicks' => 'integer',
            'ctr' => 'decimal:4',
            'engagement_rate' => 'decimal:4',
            'estimated_revenue' => 'decimal:2',
            'cost' => 'decimal:2',
            'roas' => 'decimal:4',
            'score' => 'integer',
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

    public function publishingQueueItem(): BelongsTo
    {
        return $this->belongsTo(PublishingQueueItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function insights(): HasMany
    {
        return $this->hasMany(AiInsight::class);
    }
}
