<?php

namespace App\Repositories\Eloquent;

use App\Models\PublishingJob;
use App\Models\PublishingLog;
use App\Models\User;
use App\Repositories\Contracts\PublishingJobRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentPublishingJobRepository implements PublishingJobRepositoryInterface
{
    public function create(array $attributes): PublishingJob
    {
        return PublishingJob::query()->create($attributes);
    }

    public function update(PublishingJob $job, array $attributes): PublishingJob
    {
        $job->forceFill($attributes)->save();

        return $job->refresh();
    }

    public function find(string $id): ?PublishingJob
    {
        return PublishingJob::query()->with(['queueItem.generatedContent.assets', 'socialAccount'])->find($id);
    }

    public function search(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return PublishingJob::query()
            ->with(['queueItem.generatedContent', 'socialAccount'])
            ->when($filters['workspace_id'] ?? null, fn ($query, string $workspaceId) => $query->where('workspace_id', $workspaceId))
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->when($filters['platform'] ?? null, fn ($query, string $platform) => $query->where('platform', $platform))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function log(PublishingJob $job, string $event, string $message, string $level = 'info', ?User $actor = null, array $context = []): PublishingLog
    {
        return $job->logs()->create([
            'actor_id' => $actor?->getKey(),
            'level' => $level,
            'event' => $event,
            'message' => $message,
            'context' => $context,
        ]);
    }
}
