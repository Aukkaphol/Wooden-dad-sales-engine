<?php

namespace App\Repositories\Eloquent;

use App\Models\PublishingQueueHistory;
use App\Models\PublishingQueueItem;
use App\Models\User;
use App\Models\Workspace;
use App\Repositories\Contracts\PublishingQueueRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentPublishingQueueRepository implements PublishingQueueRepositoryInterface
{
    public function create(array $attributes): PublishingQueueItem
    {
        return PublishingQueueItem::query()->create($attributes);
    }

    public function update(PublishingQueueItem $item, array $attributes): PublishingQueueItem
    {
        $item->forceFill($attributes)->save();

        return $item->refresh();
    }

    public function search(Workspace $workspace, array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return PublishingQueueItem::query()
            ->with(['brand', 'generatedContent', 'creator'])
            ->where('workspace_id', $workspace->getKey())
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('platform', 'like', "%{$search}%")
                        ->orWhere('failure_reason', 'like', "%{$search}%")
                        ->orWhereHas('generatedContent', fn ($query) => $query->where('title', 'like', "%{$search}%"));
                });
            })
            ->when($filters['brand_id'] ?? null, fn ($query, string $brandId) => $query->where('brand_id', $brandId))
            ->when($filters['content_id'] ?? null, fn ($query, string $contentId) => $query->where('generated_content_id', $contentId))
            ->when($filters['platform'] ?? null, fn ($query, string $platform) => $query->where('platform', $platform))
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->orderBy('priority')
            ->orderBy('scheduled_at')
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function createHistory(PublishingQueueItem $item, string $event, ?User $actor, array $attributes = []): PublishingQueueHistory
    {
        return $item->histories()->create([
            'actor_id' => $actor?->getKey(),
            'event' => $event,
            'previous_status' => $attributes['previous_status'] ?? null,
            'new_status' => $attributes['new_status'] ?? null,
            'comment' => $attributes['comment'] ?? null,
            'metadata' => $attributes['metadata'] ?? [],
        ]);
    }
}
