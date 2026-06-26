<?php

namespace App\Services\Pipeline;

use App\Models\AnalyticsRecord;
use App\Models\Asset;
use App\Models\Brand;
use App\Models\GeneratedContent;
use App\Models\MediaPipelineHistory;
use App\Models\MediaPipelineRun;
use App\Models\PromptTemplate;
use App\Models\PublishingQueueItem;
use App\Models\User;
use App\Models\Workspace;
use App\Repositories\Contracts\AnalyticsRecordRepositoryInterface;
use App\Repositories\Contracts\MediaPipelineRepositoryInterface;
use App\Services\ActivityLogService;
use App\Services\Analytics\AnalyticsRecordService;
use App\Services\Approvals\ContentApprovalService;
use App\Services\Contents\GeneratedContentService;
use App\Services\Publishing\PublishingQueueService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MediaPipelineService
{
    public function __construct(
        private readonly MediaPipelineRepositoryInterface $pipelines,
        private readonly GeneratedContentService $contents,
        private readonly ContentApprovalService $approvals,
        private readonly PublishingQueueService $publishing,
        private readonly AnalyticsRecordRepositoryInterface $analyticsRecords,
        private readonly AnalyticsRecordService $analytics,
        private readonly ActivityLogService $activityLog,
    ) {
    }

    public function search(Workspace $workspace, array $filters, int $perPage = 10): LengthAwarePaginator
    {
        return $this->pipelines->search($workspace, $filters, $perPage);
    }

    public function start(User $actor, Workspace $workspace, array $attributes, Request $request): MediaPipelineRun
    {
        return DB::transaction(function () use ($actor, $workspace, $attributes, $request): MediaPipelineRun {
            $brand = Brand::query()->where('workspace_id', $workspace->getKey())->findOrFail($attributes['brand_id']);
            $prompt = PromptTemplate::query()
                ->where('workspace_id', $workspace->getKey())
                ->where('brand_id', $brand->getKey())
                ->findOrFail($attributes['prompt_template_id']);
            $assetIds = $this->validatedAssetIds($workspace, $brand, $attributes['asset_ids'] ?? []);

            $content = $this->contents->create($actor, $workspace, [
                'brand_id' => $brand->getKey(),
                'prompt_template_id' => $prompt->getKey(),
                'asset_ids' => $assetIds,
                'title' => $attributes['title'],
                'platform' => $attributes['platform'],
                'content_type' => $attributes['content_type'],
                'variables' => $attributes['variables'] ?? [],
                'status' => GeneratedContent::STATUS_DRAFT,
                'tags' => $attributes['tags'] ?? ['pipeline'],
                'notes' => $attributes['notes'] ?? 'Created from Media Pipeline.',
            ], $request);

            $this->approvals->submitForReview($actor, $content, ['comment' => 'Submitted from Media Pipeline.'], $request);

            $pipeline = $this->pipelines->create([
                'workspace_id' => $workspace->getKey(),
                'brand_id' => $brand->getKey(),
                'created_by' => $actor->getKey(),
                'asset_ids' => $assetIds,
                'prompt_template_id' => $prompt->getKey(),
                'prompt_version' => $prompt->version,
                'generated_content_id' => $content->getKey(),
                'current_stage' => MediaPipelineRun::STAGE_APPROVAL,
                'status' => MediaPipelineRun::STATUS_PENDING_APPROVAL,
                'metadata' => [
                    'generation' => [
                        'platform' => $attributes['platform'],
                        'content_type' => $attributes['content_type'],
                        'variables' => $attributes['variables'] ?? [],
                        'prompt_version' => $prompt->version,
                    ],
                ],
            ]);

            $this->record($pipeline, MediaPipelineRun::STAGE_ASSETS, MediaPipelineHistory::EVENT_ASSETS_SELECTED, 'Assets selected.', $actor, null, ['asset_ids' => $assetIds], $request);
            $this->record($pipeline, MediaPipelineRun::STAGE_PROMPTS, MediaPipelineHistory::EVENT_PROMPT_SELECTED, 'Prompt selected.', $actor, $prompt, ['prompt_version' => $prompt->version], $request);
            $this->record($pipeline, MediaPipelineRun::STAGE_CONTENT, MediaPipelineHistory::EVENT_GENERATED, 'Generated content created.', $actor, $content, [], $request);
            $this->record($pipeline, MediaPipelineRun::STAGE_APPROVAL, MediaPipelineHistory::EVENT_APPROVAL_REQUESTED, 'Approval requested.', $actor, $content, [], $request);

            return $pipeline->refresh();
        });
    }

    public function approve(User $actor, MediaPipelineRun $pipeline, array $attributes, Request $request): MediaPipelineRun
    {
        $content = $this->content($pipeline);
        $this->approvals->approve($actor, $content, ['comment' => $attributes['comment'] ?? 'Approved from Media Pipeline.'], $request);

        return $this->transition($actor, $pipeline, [
            'current_stage' => MediaPipelineRun::STAGE_QUEUE,
            'status' => MediaPipelineRun::STATUS_APPROVED,
        ], MediaPipelineRun::STAGE_APPROVAL, MediaPipelineHistory::EVENT_APPROVED, 'Content approved.', $content, $request);
    }

    public function reject(User $actor, MediaPipelineRun $pipeline, array $attributes, Request $request): MediaPipelineRun
    {
        $content = $this->content($pipeline);
        $this->approvals->reject($actor, $content, ['comment' => $attributes['comment'] ?? 'Rejected from Media Pipeline.'], $request);

        return $this->transition($actor, $pipeline, [
            'current_stage' => MediaPipelineRun::STAGE_APPROVAL,
            'status' => MediaPipelineRun::STATUS_REJECTED,
        ], MediaPipelineRun::STAGE_APPROVAL, MediaPipelineHistory::EVENT_REJECTED, 'Content rejected.', $content, $request);
    }

    public function requestRevision(User $actor, MediaPipelineRun $pipeline, array $attributes, Request $request): MediaPipelineRun
    {
        $content = $this->content($pipeline);
        $this->approvals->returnWithComment($actor, $content, ['comment' => $attributes['comment'] ?? 'Revision requested from Media Pipeline.'], $request);

        return $this->transition($actor, $pipeline, [
            'current_stage' => MediaPipelineRun::STAGE_CONTENT,
            'status' => MediaPipelineRun::STATUS_REVISION_REQUESTED,
        ], MediaPipelineRun::STAGE_APPROVAL, MediaPipelineHistory::EVENT_REVISION_REQUESTED, 'Revision requested.', $content, $request);
    }

    public function queue(User $actor, Workspace $workspace, MediaPipelineRun $pipeline, array $attributes, Request $request): MediaPipelineRun
    {
        $this->ensureWorkspace($workspace, $pipeline);
        $content = $this->content($pipeline);

        if ($content->status !== GeneratedContent::STATUS_APPROVED) {
            throw ValidationException::withMessages(['status' => 'Only approved content can be queued.']);
        }

        $item = $this->publishing->schedule($actor, $workspace, [
            'generated_content_id' => $content->getKey(),
            'platform' => $attributes['platform'] ?? $content->platform,
            'scheduled_at' => $attributes['scheduled_at'] ?? null,
            'priority' => $attributes['priority'] ?? 100,
            'comment' => $attributes['comment'] ?? 'Queued from Media Pipeline.',
        ], $request);

        $status = $item->status === PublishingQueueItem::STATUS_SCHEDULED
            ? MediaPipelineRun::STATUS_SCHEDULED
            : MediaPipelineRun::STATUS_QUEUED;

        return $this->transition($actor, $pipeline, [
            'publishing_queue_item_id' => $item->getKey(),
            'current_stage' => MediaPipelineRun::STAGE_QUEUE,
            'status' => $status,
        ], MediaPipelineRun::STAGE_QUEUE, MediaPipelineHistory::EVENT_QUEUED, 'Content queued for publishing.', $item, $request);
    }

    public function publish(User $actor, MediaPipelineRun $pipeline, array $attributes, Request $request): MediaPipelineRun
    {
        $content = $this->content($pipeline);
        $queueItem = $this->queueItem($pipeline);

        $this->approvals->markPublished($actor, $content, ['comment' => $attributes['comment'] ?? 'Marked published from Media Pipeline.'], $request);
        $this->publishing->markPublished($actor, $queueItem, ['comment' => $attributes['comment'] ?? 'Marked published from Media Pipeline.'], $request);

        $record = $this->analyticsRecords->create([
            'workspace_id' => $pipeline->workspace_id,
            'brand_id' => $pipeline->brand_id,
            'generated_content_id' => $content->getKey(),
            'publishing_queue_item_id' => $queueItem->getKey(),
            'created_by' => $actor->getKey(),
            'platform' => $queueItem->platform,
            'posted_at' => now(),
            'captured_at' => now(),
            'views' => 0,
            'reach' => 0,
            'impressions' => 0,
            'likes' => 0,
            'comments' => 0,
            'shares' => 0,
            'saves' => 0,
            'follows_gained' => 0,
            'link_clicks' => 0,
            'ctr' => 0,
            'engagement_rate' => 0,
            'estimated_revenue' => 0,
            'cost' => 0,
            'roas' => 0,
            'audience_breakdown' => [],
            'metadata' => ['source' => 'media_pipeline'],
            'score' => 0,
            'score_reason' => 'Empty analytics record created after publishing.',
            'recommendation' => 'Add manual performance metrics after capture.',
        ]);

        $updated = $this->transition($actor, $pipeline, [
            'analytics_record_id' => $record->getKey(),
            'current_stage' => MediaPipelineRun::STAGE_ANALYTICS,
            'status' => MediaPipelineRun::STATUS_PUBLISHED,
        ], MediaPipelineRun::STAGE_QUEUE, MediaPipelineHistory::EVENT_PUBLISHED, 'Content marked published.', $queueItem, $request);

        $this->record($updated, MediaPipelineRun::STAGE_ANALYTICS, MediaPipelineHistory::EVENT_ANALYTICS_CREATED, 'Empty analytics record created.', $actor, $record, [], $request);

        return $updated->refresh();
    }

    public function cancel(User $actor, MediaPipelineRun $pipeline, array $attributes, Request $request): MediaPipelineRun
    {
        $queueItem = $this->queueItem($pipeline);
        $this->publishing->cancel($actor, $queueItem, ['comment' => $attributes['comment'] ?? 'Cancelled from Media Pipeline.'], $request);

        return $this->transition($actor, $pipeline, [
            'current_stage' => MediaPipelineRun::STAGE_QUEUE,
            'status' => MediaPipelineRun::STATUS_CANCELLED,
        ], MediaPipelineRun::STAGE_QUEUE, MediaPipelineHistory::EVENT_CANCELLED, 'Publishing queue item cancelled.', $queueItem, $request);
    }

    public function updateAnalytics(User $actor, MediaPipelineRun $pipeline, array $attributes, Request $request): MediaPipelineRun
    {
        $record = $pipeline->analyticsRecord;

        if ($record === null) {
            throw ValidationException::withMessages(['analytics_record_id' => 'Publish the pipeline before updating analytics.']);
        }

        $updatedRecord = $this->analytics->update($actor, $record, [
            'generated_content_id' => $pipeline->generated_content_id,
            'publishing_queue_item_id' => $pipeline->publishing_queue_item_id,
            'platform' => $attributes['platform'] ?? $record->platform,
            'posted_at' => $attributes['posted_at'] ?? $record->posted_at,
            'captured_at' => $attributes['captured_at'] ?? now(),
            'views' => $attributes['views'] ?? 0,
            'reach' => $attributes['reach'] ?? 0,
            'impressions' => $attributes['impressions'] ?? 0,
            'likes' => $attributes['likes'] ?? 0,
            'comments' => $attributes['comments'] ?? 0,
            'shares' => $attributes['shares'] ?? 0,
            'saves' => $attributes['saves'] ?? 0,
            'follows_gained' => $attributes['follows_gained'] ?? 0,
            'link_clicks' => $attributes['link_clicks'] ?? 0,
            'estimated_revenue' => $attributes['estimated_revenue'] ?? 0,
            'cost' => $attributes['cost'] ?? 0,
            'notes' => $attributes['notes'] ?? $record->notes,
            'audience_breakdown' => $attributes['audience_breakdown'] ?? [],
            'metadata' => ['source' => 'media_pipeline_update'],
        ], $request);

        $insight = $updatedRecord->insights()->latest()->first();

        $updated = $this->transition($actor, $pipeline, [
            'analytics_record_id' => $updatedRecord->getKey(),
            'ai_insight_id' => $insight?->getKey(),
            'current_stage' => MediaPipelineRun::STAGE_INSIGHTS,
            'status' => MediaPipelineRun::STATUS_INSIGHT_CREATED,
        ], MediaPipelineRun::STAGE_ANALYTICS, MediaPipelineHistory::EVENT_ANALYTICS_UPDATED, 'Analytics updated.', $updatedRecord, $request);

        if ($insight !== null) {
            $this->record($updated, MediaPipelineRun::STAGE_INSIGHTS, MediaPipelineHistory::EVENT_INSIGHT_CREATED, 'Rule-based insight created or refreshed.', $actor, $insight, [], $request);
        }

        return $updated->refresh();
    }

    private function transition(User $actor, MediaPipelineRun $pipeline, array $attributes, string $stage, string $event, string $description, object $subject, Request $request): MediaPipelineRun
    {
        return DB::transaction(function () use ($actor, $pipeline, $attributes, $stage, $event, $description, $subject, $request): MediaPipelineRun {
            $updated = $this->pipelines->update($pipeline, $attributes);
            $this->record($updated, $stage, $event, $description, $actor, $subject, [], $request);

            return $updated;
        });
    }

    private function record(MediaPipelineRun $pipeline, string $stage, string $event, string $description, User $actor, ?object $subject, array $metadata, Request $request): void
    {
        $subjectModel = $subject instanceof \Illuminate\Database\Eloquent\Model ? $subject : null;
        $this->pipelines->record($pipeline, $stage, $event, $description, $actor, $subjectModel, $metadata);
        $this->activityLog->queue('media_pipeline.'.$event, $description, $subjectModel ?? $pipeline, $metadata, $request, $actor->getKey());
    }

    private function validatedAssetIds(Workspace $workspace, Brand $brand, array $assetIds): array
    {
        $assetIds = array_values(array_unique(array_filter($assetIds)));
        $count = Asset::query()
            ->where('workspace_id', $workspace->getKey())
            ->where('brand_id', $brand->getKey())
            ->whereIn('id', $assetIds)
            ->count();

        if ($count !== count($assetIds)) {
            throw ValidationException::withMessages(['asset_ids' => 'Selected assets must belong to the selected workspace and brand.']);
        }

        return $assetIds;
    }

    private function content(MediaPipelineRun $pipeline): GeneratedContent
    {
        return $pipeline->generatedContent ?: throw ValidationException::withMessages(['generated_content_id' => 'Pipeline has no generated content.']);
    }

    private function queueItem(MediaPipelineRun $pipeline): PublishingQueueItem
    {
        return $pipeline->publishingQueueItem ?: throw ValidationException::withMessages(['publishing_queue_item_id' => 'Queue the pipeline before publishing.']);
    }

    private function ensureWorkspace(Workspace $workspace, MediaPipelineRun $pipeline): void
    {
        abort_unless($pipeline->workspace_id === $workspace->getKey(), 404);
    }
}
