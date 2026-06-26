<?php

namespace App\Jobs;

use App\DTOs\ActivityLogData;
use App\Repositories\Contracts\ActivityLogRepositoryInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class LogActivityJob implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly ActivityLogData $activityLogData)
    {
        $this->onQueue(config('jarvis.queue.activity', 'default'));
    }

    public function handle(ActivityLogRepositoryInterface $activityLogs): void
    {
        $activityLogs->create($this->activityLogData->toArray());
    }
}
