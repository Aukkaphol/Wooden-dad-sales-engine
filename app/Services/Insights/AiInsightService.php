<?php

namespace App\Services\Insights;

use App\Models\AiInsight;
use App\Models\AnalyticsRecord;
use App\Models\GeneratedContent;
use App\Models\User;
use App\Models\Workspace;
use App\Repositories\Contracts\AiInsightRepositoryInterface;
use App\Services\ActivityLogService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AiInsightService
{
    public function __construct(
        private readonly AiInsightRepositoryInterface $insights,
        private readonly ActivityLogService $activityLog,
    ) {
    }

    public function search(Workspace $workspace, array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->insights->search($workspace, $filters, $perPage);
    }

    public function create(User $actor, Workspace $workspace, array $attributes, Request $request): AiInsight
    {
        return DB::transaction(function () use ($actor, $workspace, $attributes, $request): AiInsight {
            $content = GeneratedContent::query()
                ->where('workspace_id', $workspace->getKey())
                ->findOrFail($attributes['generated_content_id']);

            $analytics = isset($attributes['analytics_record_id'])
                ? AnalyticsRecord::query()->where('workspace_id', $workspace->getKey())->findOrFail($attributes['analytics_record_id'])
                : null;

            $insight = $this->insights->create([
                'workspace_id' => $workspace->getKey(),
                'brand_id' => $content->brand_id,
                'generated_content_id' => $content->getKey(),
                'analytics_record_id' => $analytics?->getKey(),
                'created_by' => $actor->getKey(),
                'insight_type' => $attributes['insight_type'],
                'title' => $attributes['title'],
                'summary' => $attributes['summary'],
                'recommendation' => $attributes['recommendation'] ?? null,
                'priority' => $attributes['priority'],
                'status' => $attributes['status'] ?? AiInsight::STATUS_NEW,
                'metadata' => $attributes['metadata'] ?? [],
            ]);

            $this->activityLog->queue('ai_insights.created', 'AI insight created.', $insight, [], $request, $actor->getKey());

            return $insight;
        });
    }

    public function createFromAnalytics(User $actor, AnalyticsRecord $record, Request $request): AiInsight
    {
        return $this->refreshFromAnalytics($actor, $record, $request);
    }

    public function refreshFromAnalytics(User $actor, AnalyticsRecord $record, Request $request): AiInsight
    {
        $priority = $record->score >= 75 ? AiInsight::PRIORITY_HIGH : ($record->score < 40 ? AiInsight::PRIORITY_HIGH : AiInsight::PRIORITY_MEDIUM);
        $type = $record->engagement_rate < 3
            ? AiInsight::TYPE_HOOK_IMPROVEMENT
            : AiInsight::TYPE_PERFORMANCE_SUMMARY;

        $payload = [
            'workspace_id' => $record->workspace_id,
            'brand_id' => $record->brand_id,
            'generated_content_id' => $record->generated_content_id,
            'analytics_record_id' => $record->getKey(),
            'created_by' => $actor->getKey(),
            'insight_type' => $type,
            'title' => 'Performance insight for '.$record->generatedContent->title,
            'summary' => 'Score '.$record->score.'/100 from '.$record->views.' views, '.$record->engagement_rate.'% engagement, '.$record->shares.' shares, '.$record->saves.' saves, and '.$record->link_clicks.' link clicks.',
            'recommendation' => $record->recommendation,
            'priority' => $priority,
            'status' => AiInsight::STATUS_NEW,
            'metadata' => [
                'rule_based' => true,
                'score_reason' => $record->score_reason,
            ],
        ];

        $existing = AiInsight::query()->where('analytics_record_id', $record->getKey())->first();
        $insight = $existing
            ? $this->insights->update($existing, $payload)
            : $this->insights->create($payload);

        $this->activityLog->queue('ai_insights.auto_created', 'Rule-based AI insight refreshed from analytics.', $insight, [
            'analytics_record_id' => $record->getKey(),
        ], $request, $actor->getKey());

        return $insight;
    }

    public function updateStatus(User $actor, AiInsight $insight, string $status, Request $request): AiInsight
    {
        return DB::transaction(function () use ($actor, $insight, $status, $request): AiInsight {
            $updated = $this->insights->update($insight, ['status' => $status]);

            $this->activityLog->queue('ai_insights.status_updated', 'AI insight status updated.', $updated, [
                'status' => $status,
            ], $request, $actor->getKey());

            return $updated;
        });
    }

    public function delete(User $actor, AiInsight $insight, Request $request): void
    {
        DB::transaction(function () use ($actor, $insight, $request): void {
            $this->insights->delete($insight);
            $this->activityLog->queue('ai_insights.deleted', 'AI insight deleted.', $insight, [], $request, $actor->getKey());
        });
    }
}
