<?php

namespace App\Services\Publishing;

use App\Models\GeneratedContent;
use App\Models\PublishingQueueHistory;
use App\Models\PublishingQueueItem;
use App\Models\User;
use App\Models\Workspace;
use App\Repositories\Contracts\PublishingQueueRepositoryInterface;
use App\Services\ActivityLogService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PublishingQueueService
{
    public function __construct(
        private readonly PublishingQueueRepositoryInterface $queue,
        private readonly ActivityLogService $activityLog,
    ) {
    }

    public function search(Workspace $workspace, array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->queue->search($workspace, $filters, $perPage);
    }

    public function schedule(User $actor, Workspace $workspace, array $attributes, Request $request): PublishingQueueItem
    {
        return DB::transaction(function () use ($actor, $workspace, $attributes, $request): PublishingQueueItem {
            $content = GeneratedContent::query()->where('workspace_id', $workspace->getKey())->findOrFail($attributes['generated_content_id']);

            if (! in_array($content->status, [GeneratedContent::STATUS_APPROVED, GeneratedContent::STATUS_SCHEDULED], true)) {
                throw ValidationException::withMessages([
                    'generated_content_id' => 'Only approved or scheduled content can enter the publishing queue.',
                ]);
            }

            $status = filled($attributes['scheduled_at'] ?? null)
                ? PublishingQueueItem::STATUS_SCHEDULED
                : PublishingQueueItem::STATUS_WAITING;

            $item = $this->queue->create([
                'workspace_id' => $workspace->getKey(),
                'brand_id' => $content->brand_id,
                'generated_content_id' => $content->getKey(),
                'created_by' => $actor->getKey(),
                'platform' => $attributes['platform'],
                'status' => $status,
                'scheduled_at' => $attributes['scheduled_at'] ?? null,
                'priority' => $attributes['priority'],
            ]);

            $this->record($actor, $item, PublishingQueueHistory::EVENT_SCHEDULED, null, $status, $attributes['comment'] ?? null, $request);

            return $item;
        });
    }

    public function cancel(User $actor, PublishingQueueItem $item, array $attributes, Request $request): PublishingQueueItem
    {
        if (in_array($item->status, [PublishingQueueItem::STATUS_PUBLISHED, PublishingQueueItem::STATUS_CANCELLED], true)) {
            throw ValidationException::withMessages(['status' => 'This queue item can no longer be cancelled.']);
        }

        return $this->transition($actor, $item, PublishingQueueHistory::EVENT_CANCELLED, PublishingQueueItem::STATUS_CANCELLED, [
            'comment' => $attributes['comment'] ?? null,
        ], $request);
    }

    public function retry(User $actor, PublishingQueueItem $item, array $attributes, Request $request): PublishingQueueItem
    {
        if ($item->status !== PublishingQueueItem::STATUS_FAILED) {
            throw ValidationException::withMessages(['status' => 'Only failed queue items can be retried.']);
        }

        return $this->transition($actor, $item, PublishingQueueHistory::EVENT_RETRIED, PublishingQueueItem::STATUS_WAITING, [
            'retry_count' => $item->retry_count + 1,
            'failure_reason' => null,
            'comment' => $attributes['comment'] ?? null,
        ], $request);
    }

    public function markProcessing(User $actor, PublishingQueueItem $item, array $attributes, Request $request): PublishingQueueItem
    {
        return $this->transition($actor, $item, PublishingQueueHistory::EVENT_PROCESSING, PublishingQueueItem::STATUS_PROCESSING, [
            'comment' => $attributes['comment'] ?? null,
        ], $request);
    }

    public function markPublished(User $actor, PublishingQueueItem $item, array $attributes, Request $request): PublishingQueueItem
    {
        return $this->transition($actor, $item, PublishingQueueHistory::EVENT_PUBLISHED, PublishingQueueItem::STATUS_PUBLISHED, [
            'published_at' => now(),
            'comment' => $attributes['comment'] ?? null,
        ], $request);
    }

    public function markFailed(User $actor, PublishingQueueItem $item, array $attributes, Request $request): PublishingQueueItem
    {
        return $this->transition($actor, $item, PublishingQueueHistory::EVENT_FAILED, PublishingQueueItem::STATUS_FAILED, [
            'failure_reason' => $attributes['comment'] ?? 'Marked failed.',
            'comment' => $attributes['comment'] ?? null,
        ], $request);
    }

    private function transition(User $actor, PublishingQueueItem $item, string $event, string $newStatus, array $attributes, Request $request): PublishingQueueItem
    {
        return DB::transaction(function () use ($actor, $item, $event, $newStatus, $attributes, $request): PublishingQueueItem {
            $previousStatus = $item->status;
            $payload = [
                'status' => $newStatus,
                'published_at' => $attributes['published_at'] ?? $item->published_at,
                'retry_count' => $attributes['retry_count'] ?? $item->retry_count,
            ];

            if (array_key_exists('failure_reason', $attributes)) {
                $payload['failure_reason'] = $attributes['failure_reason'];
            } elseif ($item->failure_reason !== null) {
                $payload['failure_reason'] = $item->failure_reason;
            }

            $updated = $this->queue->update($item, $payload);

            $this->record($actor, $updated, $event, $previousStatus, $newStatus, $attributes['comment'] ?? null, $request);

            return $updated;
        });
    }

    private function record(?User $actor, PublishingQueueItem $item, string $event, ?string $previousStatus, ?string $newStatus, ?string $comment, Request $request): void
    {
        $this->queue->createHistory($item, $event, $actor, [
            'previous_status' => $previousStatus,
            'new_status' => $newStatus,
            'comment' => $comment,
        ]);

        $this->activityLog->queue(
            event: 'publishing_queue.'.$event,
            description: 'Publishing queue event recorded.',
            subject: $item,
            properties: ['previous_status' => $previousStatus, 'new_status' => $newStatus],
            request: $request,
            userId: $actor?->getKey(),
        );
    }
}
