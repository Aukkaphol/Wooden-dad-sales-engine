<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MediaPipelineHistory extends Model
{
    use HasUuid;

    public const EVENT_ASSETS_SELECTED = 'assets_selected';
    public const EVENT_PROMPT_SELECTED = 'prompt_selected';
    public const EVENT_GENERATED = 'generated';
    public const EVENT_APPROVAL_REQUESTED = 'approval_requested';
    public const EVENT_APPROVED = 'approved';
    public const EVENT_REJECTED = 'rejected';
    public const EVENT_REVISION_REQUESTED = 'revision_requested';
    public const EVENT_QUEUED = 'queued';
    public const EVENT_PUBLISHED = 'published';
    public const EVENT_ANALYTICS_CREATED = 'analytics_created';
    public const EVENT_ANALYTICS_UPDATED = 'analytics_updated';
    public const EVENT_INSIGHT_CREATED = 'insight_created';
    public const EVENT_CANCELLED = 'cancelled';

    protected $fillable = [
        'media_pipeline_run_id',
        'actor_id',
        'stage',
        'event',
        'description',
        'subject_type',
        'subject_id',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function pipelineRun(): BelongsTo
    {
        return $this->belongsTo(MediaPipelineRun::class, 'media_pipeline_run_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
