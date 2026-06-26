<?php

namespace App\Services\Publishing;

use App\Jobs\ProcessPublishingJob;
use App\Models\PublishingJob;
use App\Models\PublishingQueueItem;
use App\Models\SocialAccount;
use App\Models\User;
use App\Repositories\Contracts\PublishingJobRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PublishingSchedulerService
{
    public function __construct(
        private readonly PublishingJobService $jobs,
        private readonly PublishingJobRepositoryInterface $jobRepository,
    ) {
    }

    public function publishNow(User $actor, PublishingQueueItem $item, ?SocialAccount $account = null): PublishingJob
    {
        return DB::transaction(function () use ($actor, $item, $account): PublishingJob {
            $this->ensurePublishable($item);
            $job = $this->jobs->createForQueueItem($item, $account, $actor);
            ProcessPublishingJob::dispatch($job->getKey());

            return $job;
        });
    }

    public function schedule(User $actor, PublishingQueueItem $item, string $scheduledAt, ?SocialAccount $account = null): PublishingJob
    {
        return DB::transaction(function () use ($actor, $item, $scheduledAt, $account): PublishingJob {
            $this->ensurePublishable($item);
            $job = $this->jobs->createForQueueItem($item, $account, $actor, $scheduledAt);
            ProcessPublishingJob::dispatch($job->getKey())->delay(Carbon::parse($scheduledAt));

            return $job;
        });
    }

    public function retry(User $actor, PublishingJob $job): PublishingJob
    {
        if ($job->status !== PublishingJob::STATUS_FAILED) {
            throw ValidationException::withMessages(['status' => 'Only failed publishing jobs can be retried.']);
        }

        $queued = $this->jobRepository->update($job, [
            'status' => PublishingJob::STATUS_QUEUED,
            'failure_reason' => null,
            'finished_at' => null,
        ]);
        $this->jobs->log($queued, 'retried', 'Publishing job queued for retry.', 'info', $actor);
        ProcessPublishingJob::dispatch($queued->getKey());

        return $queued;
    }

    public function cancel(User $actor, PublishingJob $job, ?string $reason = null): PublishingJob
    {
        if (in_array($job->status, [PublishingJob::STATUS_PUBLISHED, PublishingJob::STATUS_CANCELLED], true)) {
            throw ValidationException::withMessages(['status' => 'This publishing job can no longer be cancelled.']);
        }

        return $this->jobs->cancel($job, $actor, $reason);
    }

    private function ensurePublishable(PublishingQueueItem $item): void
    {
        if (in_array($item->status, [PublishingQueueItem::STATUS_CANCELLED, PublishingQueueItem::STATUS_PUBLISHED], true)) {
            throw ValidationException::withMessages(['status' => 'This queue item cannot be scheduled for publishing.']);
        }
    }
}
