<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Database\Factories\BrandFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    /** @use HasFactory<BrandFactory> */
    use HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'workspace_id',
        'name',
        'slug',
        'logo_path',
        'primary_color',
        'secondary_color',
        'font_family',
        'tone',
        'voice',
        'default_prompt',
        'default_cta',
        'contact_information',
        'social_links',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'contact_information' => 'array',
            'social_links' => 'array',
        ];
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    public function promptTemplates(): HasMany
    {
        return $this->hasMany(PromptTemplate::class);
    }

    public function generatedContents(): HasMany
    {
        return $this->hasMany(GeneratedContent::class);
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

    public function socialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function publishingJobs(): HasMany
    {
        return $this->hasMany(PublishingJob::class);
    }
}
