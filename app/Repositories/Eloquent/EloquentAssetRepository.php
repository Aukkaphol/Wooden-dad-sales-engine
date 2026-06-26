<?php

namespace App\Repositories\Eloquent;

use App\Models\Asset;
use App\Models\Workspace;
use App\Repositories\Contracts\AssetRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentAssetRepository implements AssetRepositoryInterface
{
    public function create(array $attributes): Asset
    {
        $asset = new Asset();
        $asset->forceFill($attributes)->save();

        return $asset->refresh();
    }

    public function update(Asset $asset, array $attributes): Asset
    {
        $asset->forceFill($attributes)->save();

        return $asset->refresh();
    }

    public function delete(Asset $asset): bool
    {
        return (bool) $asset->delete();
    }

    public function search(Workspace $workspace, array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return Asset::query()
            ->with(['brand', 'uploader'])
            ->where('workspace_id', $workspace->getKey())
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                });
            })
            ->when($filters['brand_id'] ?? null, fn ($query, string $brandId) => $query->where('brand_id', $brandId))
            ->when($filters['type'] ?? null, fn ($query, string $type) => $query->where('type', $type))
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->when($filters['category'] ?? null, fn ($query, string $category) => $query->where('category', $category))
            ->when($filters['tag'] ?? null, fn ($query, string $tag) => $query->whereJsonContains('tags', strtolower($tag)))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }
}
