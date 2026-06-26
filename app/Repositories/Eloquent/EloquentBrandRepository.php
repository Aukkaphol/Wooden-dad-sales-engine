<?php

namespace App\Repositories\Eloquent;

use App\Models\Brand;
use App\Models\Workspace;
use App\Repositories\Contracts\BrandRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentBrandRepository implements BrandRepositoryInterface
{
    public function create(Workspace $workspace, array $attributes): Brand
    {
        return $workspace->brands()->create($attributes);
    }

    public function update(Brand $brand, array $attributes): Brand
    {
        $brand->forceFill($attributes)->save();

        return $brand->refresh();
    }

    public function delete(Brand $brand): bool
    {
        return (bool) $brand->delete();
    }

    public function paginateForWorkspace(Workspace $workspace, int $perPage = 15): LengthAwarePaginator
    {
        return Brand::query()
            ->where('workspace_id', $workspace->getKey())
            ->latest()
            ->paginate($perPage);
    }

    public function slugExists(Workspace $workspace, string $slug): bool
    {
        return Brand::query()
            ->where('workspace_id', $workspace->getKey())
            ->where('slug', $slug)
            ->exists();
    }
}
