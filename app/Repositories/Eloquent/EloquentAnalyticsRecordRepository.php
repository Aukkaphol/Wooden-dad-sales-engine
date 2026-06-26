<?php

namespace App\Repositories\Eloquent;

use App\Models\AnalyticsRecord;
use App\Models\Workspace;
use App\Repositories\Contracts\AnalyticsRecordRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentAnalyticsRecordRepository implements AnalyticsRecordRepositoryInterface
{
    public function create(array $attributes): AnalyticsRecord
    {
        return AnalyticsRecord::query()->create($attributes);
    }

    public function update(AnalyticsRecord $record, array $attributes): AnalyticsRecord
    {
        $record->forceFill($attributes)->save();

        return $record->refresh();
    }

    public function delete(AnalyticsRecord $record): bool
    {
        return (bool) $record->delete();
    }

    public function search(Workspace $workspace, array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return AnalyticsRecord::query()
            ->with(['brand', 'generatedContent', 'publishingQueueItem'])
            ->where('workspace_id', $workspace->getKey())
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('platform', 'like', "%{$search}%")
                        ->orWhere('notes', 'like', "%{$search}%")
                        ->orWhereHas('generatedContent', fn ($query) => $query->where('title', 'like', "%{$search}%"));
                });
            })
            ->when($filters['brand_id'] ?? null, fn ($query, string $brandId) => $query->where('brand_id', $brandId))
            ->when($filters['platform'] ?? null, fn ($query, string $platform) => $query->where('platform', $platform))
            ->when($filters['content_type'] ?? null, fn ($query, string $type) => $query->whereHas('generatedContent', fn ($query) => $query->where('content_type', $type)))
            ->when($filters['date_from'] ?? null, fn ($query, string $date) => $query->whereDate('captured_at', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, string $date) => $query->whereDate('captured_at', '<=', $date))
            ->latest('captured_at')
            ->paginate($perPage)
            ->withQueryString();
    }
}
