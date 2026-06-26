<?php

namespace App\Services\Publishing;

use App\Models\PublishingJob;
use App\Models\PublishingLog;
use App\Models\PublishingQueueItem;
use App\Models\SocialAccount;
use App\Models\User;
use App\Repositories\Contracts\PublishingJobRepositoryInterface;
use App\Services\ActivityLogService;
use Throwable;

class PublishingJobService
{
    public function __construct(
        private readonly PublishingJobRepositoryInterface $jobs,
        private readonly SocialPublishingService $publishing,
        private readonly ActivityLogService $activityLog,
    ) {
    }

    public function createForQueueItem(PublishingQueueItem $item, ?SocialAccount $account, ?User $actor, ?string $scheduledAt = null): PublishingJob
    {
        $status = $scheduledAt ? PublishingJob::STATUS_SCHEDULED : PublishingJob::STATUS_QUEUED;

        $job = $this->jobs->create([
            'workspace_id' => $item->workspace_id,
            'brand_id' => $item->brand_id,
            'publishing_queue_item_id' => $item->getKey(),
            'social_account_id' => $account?->getKey(),
            'created_by' => $actor?->getKey(),
            'platform' => $item->platform,
            'status' => $status,
            'scheduled_at' => $scheduledAt,
            'metadata' => ['source' => 'publishing_scheduler'],
        ]);

        $this->log($job, 'created', 'Publishing job created.', PublishingLog::LEVEL_INFO, $actor);

        return $job;
    }

    public function markProcessing(PublishingJob $job): PublishingJob
    {
        $updated = $this->jobs->update($job, [
            'status' => PublishingJob::STATUS_PROCESSING,
            'started_at' => now(),
            'attempts' => $job->attempts + 1,
        ]);

        $this->log($updated, 'processing', 'Publishing job processing started.');

        return $updated;
    }

    public function markPublished(PublishingJob $job, string $providerPostId, array $providerResponse = []): PublishingJob
    {
        $updated = $this->jobs->update($job, [
            'status' => PublishingJob::STATUS_PUBLISHED,
            'finished_at' => now(),
            'provider_post_id' => $providerPostId,
            'provider_response' => $providerResponse,
            'failure_reason' => null,
        ]);

        $this->log($updated, 'published', 'Publishing job completed.');

        return $updated;
    }

    public function markFailed(PublishingJob $job, string $reason): PublishingJob
    {
        $updated = $this->jobs->update($job, [
            'status' => PublishingJob::STATUS_FAILED,
            'finished_at' => now(),
            'failure_reason' => $reason,
        ]);

        $this->log($updated, 'failed', $reason, PublishingLog::LEVEL_ERROR);

        return $updated;
    }

    public function cancel(PublishingJob $job, ?User $actor = null, ?string $reason = null): PublishingJob
    {
        $updated = $this->jobs->update($job, [
            'status' => PublishingJob::STATUS_CANCELLED,
            'finished_at' => now(),
            'failure_reason' => $reason,
        ]);

        $this->log($updated, 'cancelled', $reason ?? 'Publishing job cancelled.', PublishingLog::LEVEL_WARNING, $actor);

        return $updated;
    }

    public function process(string $publishingJobId): void
    {
        $job = $this->jobs->find($publishingJobId);

        if (! $job || $job->status === PublishingJob::STATUS_CANCELLED) {
            return;
        }

        if ($job->socialAccount === null) {
            $this->markFailed($this->markProcessing($job), 'No social account is connected for this publishing job.');

            return;
        }

        $processing = $this->markProcessing($job);

        try {
            $result = $this->publishing->publish($processing->queueItem, $processing->socialAccount);
            $this->markPublished($processing, $result->providerPostId, $result->payload);
        } catch (Throwable $exception) {
            $this->markFailed($processing, $exception->getMessage());
        }
    }

    public function log(PublishingJob $job, string $event, string $message, string $level = PublishingLog::LEVEL_INFO, ?User $actor = null, array $context = []): void
    {
        $this->jobs->log($job, $event, $message, $level, $actor, $context);
        $this->activityLog->queue('publishing_job.'.$event, $message, $job, $context, userId: $actor?->getKey());
    }
}
