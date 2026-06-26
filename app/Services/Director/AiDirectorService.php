<?php

namespace App\Services\Director;

use App\Models\AnalyticsRecord;
use App\Models\GeneratedContent;
use App\Models\Workspace;

class AiDirectorService
{
    public function __construct(
        private readonly RecommendationEngine $recommendations,
        private readonly ContentScoreService $scores,
    ) {
    }

    public function decisions(Workspace $workspace, array $filters = []): array
    {
        $brandId = $filters['brand_id'] ?? null;
        $analytics = AnalyticsRecord::query()
            ->with('generatedContent')
            ->where('workspace_id', $workspace->getKey())
            ->when($brandId, fn ($query) => $query->where('brand_id', $brandId))
            ->latest('captured_at')
            ->get();

        $content = GeneratedContent::query()
            ->with(['assets', 'analyticsRecords'])
            ->where('workspace_id', $workspace->getKey())
            ->when($brandId, fn ($query) => $query->where('brand_id', $brandId))
            ->latest()
            ->first();

        $contentScore = $this->recommendations->contentScore($content, $this->scores->score($content));
        $prompt = $this->recommendations->prompt($workspace, $brandId);
        $asset = $this->recommendations->asset($workspace, $brandId);
        $platform = $this->recommendations->platform($analytics);
        $publishTime = $this->recommendations->publishTime($analytics);
        $campaign = $this->recommendations->campaign($analytics);
        $audience = $this->recommendations->audience($analytics);
        $contentType = $this->recommendations->contentType($analytics);
        $today = $this->recommendations->today($prompt, $asset, $platform, $publishTime, $campaign, $audience, $contentType, $contentScore);

        return [
            'today' => $today,
            'best_posting_time' => $publishTime,
            'suggested_platform' => $platform,
            'suggested_asset' => $asset,
            'suggested_prompt' => $prompt,
            'content_quality_score' => $contentScore,
            'campaign' => $campaign,
            'audience' => $audience,
            'content_type' => $contentType,
        ];
    }

    public function dashboardWidgets(Workspace $workspace, array $filters = []): array
    {
        return collect($this->decisions($workspace, $filters))
            ->only(['today', 'best_posting_time', 'suggested_platform', 'suggested_asset', 'suggested_prompt', 'content_quality_score'])
            ->all();
    }
}
