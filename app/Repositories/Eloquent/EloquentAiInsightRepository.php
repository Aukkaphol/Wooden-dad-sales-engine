<?php

namespace App\Repositories\Eloquent;

use App\Models\AiInsight;
use App\Models\Workspace;
use App\Repositories\Contracts\AiInsightRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentAiInsightRepository implements AiInsightRepositoryInterface
{
    public function create(array $attributes): AiInsight
    {
        return AiInsight::query()->create($attributes);
    }

    public function update(AiInsight $insight, array $attributes): AiInsight
    {
        $insight->forceFill($attributes)->save();

        return $insight->refresh();
    }

    public function delete(AiInsight $insight): bool
    {
        return (bool) $insight->delete();
    }

    public function search(Workspace $workspace, array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return AiInsight::query()
            ->with(['brand', 'generatedContent', 'analyticsRecord'])
            ->where('workspace_id', $workspace->getKey())
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('summary', 'like', "%{$search}%")
                        ->orWhere('recommendation', 'like', "%{$search}%")
                        ->orWhereHas('generatedContent', fn ($query) => $query->where('title', 'like', "%{$search}%"));
                });
            })
            ->when($filters['brand_id'] ?? null, fn ($query, string $brandId) => $query->where('brand_id', $brandId))
            ->when($filters['content_id'] ?? null, fn ($query, string $contentId) => $query->where('generated_content_id', $contentId))
            ->when($filters['insight_type'] ?? null, fn ($query, string $type) => $query->where('insight_type', $type))
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->when($filters['priority'] ?? null, fn ($query, string $priority) => $query->where('priority', $priority))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }
}
