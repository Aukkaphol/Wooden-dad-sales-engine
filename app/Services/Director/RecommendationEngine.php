<?php

namespace App\Services\Director;

use App\DTOs\MarketingDecision;
use App\Models\AnalyticsRecord;
use App\Models\Asset;
use App\Models\GeneratedContent;
use App\Models\PromptTemplate;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class RecommendationEngine
{
    public function contentScore(?GeneratedContent $content, array $score): MarketingDecision
    {
        return new MarketingDecision(
            type: 'content_quality_score',
            title: 'Content quality score',
            recommendation: $content ? $content->title.' scores '.$score['score'].'/100.' : 'Create a content draft to unlock scoring.',
            confidence: $score['confidence'],
            reasoning: $score['reasoning'],
            metadata: ['score' => $score['score']],
        );
    }

    public function prompt(Workspace $workspace, ?string $brandId = null): MarketingDecision
    {
        $prompt = $this->brandScoped(PromptTemplate::query()->where('workspace_id', $workspace->getKey()), $brandId)
            ->orderByDesc('rating_average')
            ->orderByDesc('success_rate')
            ->orderByDesc('usage_count')
            ->first();

        return new MarketingDecision(
            type: 'prompt_recommendation',
            title: 'Suggested prompt',
            recommendation: $prompt ? 'Use "'.$prompt->title.'" for the next draft.' : 'Create an active prompt template for this workspace.',
            confidence: $prompt ? 78 : 25,
            reasoning: $prompt ? 'Selected from rating, success rate, and usage history.' : 'No prompt template exists for this filter.',
            metadata: ['prompt_id' => $prompt?->getKey()],
        );
    }

    public function asset(Workspace $workspace, ?string $brandId = null): MarketingDecision
    {
        $asset = $this->brandScoped(Asset::query()->where('workspace_id', $workspace->getKey()), $brandId)
            ->where('status', Asset::STATUS_READY)
            ->latest()
            ->first();

        return new MarketingDecision(
            type: 'asset_recommendation',
            title: 'Suggested asset',
            recommendation: $asset ? 'Use "'.$asset->name.'" as the next creative asset.' : 'Prepare a ready asset before generating the next post.',
            confidence: $asset ? 64 : 30,
            reasoning: $asset ? 'Selected from ready assets, prioritizing the most recent brand creative.' : 'No ready asset exists for this filter.',
            metadata: ['asset_id' => $asset?->getKey()],
        );
    }

    public function platform(Collection $analytics): MarketingDecision
    {
        $platform = $analytics->groupBy('platform')
            ->map(fn ($records) => round($records->avg('score'), 2))
            ->sortDesc()
            ->keys()
            ->first();

        return new MarketingDecision(
            type: 'platform_recommendation',
            title: 'Suggested platform',
            recommendation: $platform ? 'Prioritize '.$platform.' for the next publish window.' : 'Publish a few pieces first so platform performance can be compared.',
            confidence: $platform ? 76 : 25,
            reasoning: $platform ? 'Chosen from the platform with the highest average content score.' : 'No analytics records are available yet.',
            metadata: ['platform' => $platform],
        );
    }

    public function publishTime(Collection $analytics): MarketingDecision
    {
        $hour = $analytics->filter(fn (AnalyticsRecord $record) => $record->posted_at !== null)
            ->groupBy(fn (AnalyticsRecord $record) => $record->posted_at->format('H:00'))
            ->map(fn ($records) => round($records->avg('score'), 2))
            ->sortDesc()
            ->keys()
            ->first();

        return new MarketingDecision(
            type: 'publish_time_recommendation',
            title: 'Best posting time',
            recommendation: $hour ? 'Schedule the next post around '.$hour.'.' : 'Use 19:00 as a starting test window until analytics history grows.',
            confidence: $hour ? 72 : 35,
            reasoning: $hour ? 'Selected from the posting hour with the strongest average score.' : 'Fallback recommendation because no posted-at analytics history exists.',
            metadata: ['hour' => $hour ?? '19:00'],
        );
    }

    public function campaign(Collection $analytics): MarketingDecision
    {
        $best = $analytics->sortByDesc('score')->first();

        return new MarketingDecision(
            type: 'campaign_recommendation',
            title: 'Campaign recommendation',
            recommendation: $best ? 'Build a follow-up campaign from "'.$best->generatedContent->title.'".' : 'Run a small campaign test with one prompt, one ready asset, and one platform.',
            confidence: $best ? 74 : 30,
            reasoning: $best ? 'Uses the highest-scoring content as the campaign seed.' : 'No performance winner is available yet.',
            metadata: ['generated_content_id' => $best?->generated_content_id],
        );
    }

    public function audience(Collection $analytics): MarketingDecision
    {
        $gender = $this->topAudienceValue($analytics, 'gender');
        $age = $this->topAudienceValue($analytics, 'age');

        return new MarketingDecision(
            type: 'audience_recommendation',
            title: 'Audience recommendation',
            recommendation: $gender || $age ? 'Aim the next creative at '.trim(($gender ?? '').' '.($age ?? '')).'.' : 'Capture audience breakdown on the next analytics update.',
            confidence: $gender || $age ? 70 : 25,
            reasoning: $gender || $age ? 'Uses the dominant audience segments from stored analytics JSON.' : 'Audience JSON is missing or too sparse.',
            metadata: ['gender' => $gender, 'age' => $age],
        );
    }

    public function contentType(Collection $analytics): MarketingDecision
    {
        $type = $analytics->filter(fn (AnalyticsRecord $record) => $record->generatedContent !== null)
            ->groupBy(fn (AnalyticsRecord $record) => $record->generatedContent->content_type)
            ->map(fn ($records) => round($records->avg('score'), 2))
            ->sortDesc()
            ->keys()
            ->first();

        return new MarketingDecision(
            type: 'content_type_recommendation',
            title: 'Content type recommendation',
            recommendation: $type ? 'Create another '.str_replace('_', ' ', $type).' next.' : 'Start with a Facebook post or short caption to collect baseline results.',
            confidence: $type ? 73 : 35,
            reasoning: $type ? 'Selected from the content type with the highest average score.' : 'No scored content type history exists yet.',
            metadata: ['content_type' => $type],
        );
    }

    public function today(MarketingDecision ...$decisions): MarketingDecision
    {
        $best = collect($decisions)->sortByDesc('confidence')->first();

        return new MarketingDecision(
            type: 'todays_recommendation',
            title: "Today's recommendation",
            recommendation: $best?->recommendation ?? 'Create one draft and capture performance data today.',
            confidence: $best?->confidence ?? 30,
            reasoning: $best ? 'Chosen from the highest-confidence recommendation generated today.' : 'Fallback because no decision inputs are available.',
            metadata: ['source_type' => $best?->type],
        );
    }

    private function brandScoped(Builder $query, ?string $brandId): Builder
    {
        return $query->when($brandId, fn ($query) => $query->where('brand_id', $brandId));
    }

    private function topAudienceValue(Collection $analytics, string $key): ?string
    {
        return $analytics
            ->flatMap(fn (AnalyticsRecord $record) => $record->audience_breakdown[$key] ?? [])
            ->sortDesc()
            ->keys()
            ->first();
    }
}
