<?php

namespace App\Services\Director;

use App\Models\GeneratedContent;

class ContentScoreService
{
    public function score(?GeneratedContent $content): array
    {
        if ($content === null) {
            return [
                'score' => 0,
                'confidence' => 20,
                'reasoning' => 'No generated content is available yet.',
            ];
        }

        $latestAnalytics = $content->analyticsRecords->sortByDesc('captured_at')->first();
        $analyticsScore = $latestAnalytics?->score ?? 0;
        $hasAssets = $content->assets->isNotEmpty();
        $hasPromptSnapshot = filled($content->prompt_snapshot);
        $hasTags = ! empty($content->tags);
        $isApprovedOrPublished = in_array($content->status, [
            GeneratedContent::STATUS_APPROVED,
            GeneratedContent::STATUS_SCHEDULED,
            GeneratedContent::STATUS_PUBLISHED,
        ], true);

        $score = min(100, (int) round(
            ($analyticsScore * 0.45)
            + ($hasAssets ? 15 : 0)
            + ($hasPromptSnapshot ? 15 : 0)
            + ($hasTags ? 10 : 0)
            + ($isApprovedOrPublished ? 15 : 5)
        ));

        return [
            'score' => $score,
            'confidence' => $latestAnalytics ? 82 : 55,
            'reasoning' => $latestAnalytics
                ? 'Score combines latest analytics performance with content readiness signals.'
                : 'Score is based on content readiness because analytics data is not available yet.',
        ];
    }
}
