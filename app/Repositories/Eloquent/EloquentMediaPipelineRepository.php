<?php

namespace App\Repositories\Eloquent;

use App\Models\MediaPipelineHistory;
use App\Models\MediaPipelineRun;
use App\Models\User;
use App\Models\Workspace;
use App\Repositories\Contracts\MediaPipelineRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

class EloquentMediaPipelineRepository implements MediaPipelineRepositoryInterface
{
    public function create(array $attributes): MediaPipelineRun
    {
        return MediaPipelineRun::query()->create($attributes);
    }

    public function update(MediaPipelineRun $pipeline, array $attributes): MediaPipelineRun
    {
        $pipeline->forceFill($attributes)->save();

        return $pipeline->refresh();
    }

    public function search(Workspace $workspace, array $filters, int $perPage = 10): LengthAwarePaginator
    {
        return MediaPipelineRun::query()
            ->with(['brand', 'generatedContent', 'promptTemplate', 'publishingQueueItem', 'analyticsRecord', 'aiInsight'])
            ->where('workspace_id', $workspace->getKey())
            ->when($filters['brand_id'] ?? null, fn ($query, string $brandId) => $query->where('brand_id', $brandId))
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->when($filters['stage'] ?? null, fn ($query, string $stage) => $query->where('current_stage', $stage))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function record(MediaPipelineRun $pipeline, string $stage, string $event, string $description, ?User $actor = null, ?Model $subject = null, array $metadata = []): MediaPipelineHistory
    {
        return $pipeline->histories()->create([
            'actor_id' => $actor?->getKey(),
            'stage' => $stage,
            'event' => $event,
            'description' => $description,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'metadata' => $metadata,
        ]);
    }
}
