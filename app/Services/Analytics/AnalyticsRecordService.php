<?php

namespace App\Services\Analytics;

use App\Models\AnalyticsRecord;
use App\Models\GeneratedContent;
use App\Models\PublishingQueueItem;
use App\Models\User;
use App\Models\Workspace;
use App\Repositories\Contracts\AnalyticsRecordRepositoryInterface;
use App\Services\ActivityLogService;
use App\Services\Insights\AiInsightService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AnalyticsRecordService
{
    public function __construct(
        private readonly AnalyticsRecordRepositoryInterface $records,
        private readonly ActivityLogService $activityLog,
        private readonly AiInsightService $insights,
    ) {
    }

    public function search(Workspace $workspace, array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->records->search($workspace, $filters, $perPage);
    }

    public function create(User $actor, Workspace $workspace, array $attributes, Request $request): AnalyticsRecord
    {
        return DB::transaction(function () use ($actor, $workspace, $attributes, $request): AnalyticsRecord {
            $content = GeneratedContent::query()->where('workspace_id', $workspace->getKey())->findOrFail($attributes['generated_content_id']);
            $queueItem = isset($attributes['publishing_queue_item_id'])
                ? PublishingQueueItem::query()->where('workspace_id', $workspace->getKey())->findOrFail($attributes['publishing_queue_item_id'])
                : null;
            $this->ensureQueueItemMatchesContent($queueItem, $content);

            $record = $this->records->create($this->payload($actor, $workspace, $content, $queueItem, $attributes));
            $this->insights->createFromAnalytics($actor, $record, $request);
            $this->activityLog->queue('analytics.created', 'Analytics record created.', $record, [], $request, $actor->getKey());

            return $record;
        });
    }

    public function update(User $actor, AnalyticsRecord $record, array $attributes, Request $request): AnalyticsRecord
    {
        return DB::transaction(function () use ($actor, $record, $attributes, $request): AnalyticsRecord {
            $content = GeneratedContent::query()->where('workspace_id', $record->workspace_id)->findOrFail($attributes['generated_content_id']);
            $queueItem = isset($attributes['publishing_queue_item_id'])
                ? PublishingQueueItem::query()->where('workspace_id', $record->workspace_id)->findOrFail($attributes['publishing_queue_item_id'])
                : null;
            $this->ensureQueueItemMatchesContent($queueItem, $content);

            $updated = $this->records->update($record, $this->payload($actor, $record->workspace, $content, $queueItem, $attributes, false));
            $this->insights->refreshFromAnalytics($actor, $updated, $request);
            $this->activityLog->queue('analytics.updated', 'Analytics record updated.', $updated, [], $request, $actor->getKey());

            return $updated;
        });
    }

    public function delete(User $actor, AnalyticsRecord $record, Request $request): void
    {
        DB::transaction(function () use ($actor, $record, $request): void {
            $this->records->delete($record);
            $this->activityLog->queue('analytics.deleted', 'Analytics record deleted.', $record, [], $request, $actor->getKey());
        });
    }

    public function score(array $metrics): array
    {
        $views = max((int) ($metrics['views'] ?? 0), 0);
        $engagementRate = (float) ($metrics['engagement_rate'] ?? 0);
        $follows = (int) ($metrics['follows_gained'] ?? 0);
        $shares = (int) ($metrics['shares'] ?? 0);
        $saves = (int) ($metrics['saves'] ?? 0);
        $clicks = (int) ($metrics['link_clicks'] ?? 0);

        $score = min(100, (int) round(
            min(30, $views / 100)
            + min(25, $engagementRate * 2.5)
            + min(15, $follows * 2)
            + min(10, $shares)
            + min(10, $saves)
            + min(10, $clicks / 2)
        ));

        $reason = "Views contributed {$views}; engagement rate is {$engagementRate}%; follows {$follows}; shares {$shares}; saves {$saves}; clicks {$clicks}.";
        $recommendation = $this->recommendation($metrics, $score);

        return [$score, $reason, $recommendation];
    }

    private function payload(User $actor, Workspace $workspace, GeneratedContent $content, ?PublishingQueueItem $queueItem, array $attributes, bool $includeCreator = true): array
    {
        $metrics = $this->metrics($attributes);
        [$score, $reason, $recommendation] = $this->score($metrics + ['audience_breakdown' => $attributes['audience_breakdown'] ?? []]);

        return array_filter([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $content->brand_id,
            'generated_content_id' => $content->getKey(),
            'publishing_queue_item_id' => $queueItem?->getKey(),
            'created_by' => $includeCreator ? $actor->getKey() : null,
            'platform' => $attributes['platform'],
            'posted_at' => $attributes['posted_at'] ?? null,
            'captured_at' => $attributes['captured_at'],
            ...$metrics,
            'notes' => $attributes['notes'] ?? null,
            'audience_breakdown' => $attributes['audience_breakdown'] ?? [],
            'metadata' => $attributes['metadata'] ?? [],
            'score' => $score,
            'score_reason' => $reason,
            'recommendation' => $recommendation,
        ], fn ($value) => $value !== null);
    }

    private function metrics(array $attributes): array
    {
        $views = (int) ($attributes['views'] ?? 0);
        $impressions = (int) ($attributes['impressions'] ?? 0);
        $likes = (int) ($attributes['likes'] ?? 0);
        $comments = (int) ($attributes['comments'] ?? 0);
        $shares = (int) ($attributes['shares'] ?? 0);
        $saves = (int) ($attributes['saves'] ?? 0);
        $clicks = (int) ($attributes['link_clicks'] ?? 0);
        $revenue = (float) ($attributes['estimated_revenue'] ?? 0);
        $cost = (float) ($attributes['cost'] ?? 0);

        return [
            'views' => $views,
            'reach' => (int) ($attributes['reach'] ?? 0),
            'impressions' => $impressions,
            'likes' => $likes,
            'comments' => $comments,
            'shares' => $shares,
            'saves' => $saves,
            'follows_gained' => (int) ($attributes['follows_gained'] ?? 0),
            'link_clicks' => $clicks,
            'ctr' => $impressions > 0 ? round(($clicks / $impressions) * 100, 4) : 0,
            'engagement_rate' => $views > 0 ? round((($likes + $comments + $shares + $saves) / $views) * 100, 4) : 0,
            'estimated_revenue' => $revenue,
            'cost' => $cost,
            'roas' => $cost > 0 ? round($revenue / $cost, 4) : 0,
        ];
    }

    private function ensureQueueItemMatchesContent(?PublishingQueueItem $queueItem, GeneratedContent $content): void
    {
        if ($queueItem !== null && $queueItem->generated_content_id !== $content->getKey()) {
            throw ValidationException::withMessages([
                'publishing_queue_item_id' => 'The selected queue item must belong to the selected generated content.',
            ]);
        }
    }

    private function recommendation(array $metrics, int $score): string
    {
        $audience = $metrics['audience_breakdown'] ?? [];
        $gender = collect($audience['gender'] ?? [])->sortDesc()->keys()->first();
        $age = collect($audience['age'] ?? [])->sortDesc()->keys()->first();
        $prefix = $gender && $age ? "Audience is strong among {$gender} {$age}. " : '';

        if ($score >= 75) {
            return $prefix.'Performance is strong. Reuse the core hook and test a Reel or short video variant at peak posting time.';
        }

        if (($metrics['engagement_rate'] ?? 0) < 3) {
            return $prefix.'Engagement is low. Try a shorter caption, stronger hook, clearer CTA, and a Reel version around 19:00.';
        }

        return $prefix.'Performance is moderate. Test a stronger opening line, more specific offer, and alternate creative format.';
    }
}
