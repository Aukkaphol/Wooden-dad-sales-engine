<?php

namespace App\Jobs;

use App\Services\Publishing\PublishingJobService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;

class ProcessPublishingJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(public readonly string $publishingJobId)
    {
        $this->onQueue(config('jarvis.queue.publishing', 'publishing'));
    }

    public function handle(PublishingJobService $jobs): void
    {
        $jobs->process($this->publishingJobId);
    }
}
