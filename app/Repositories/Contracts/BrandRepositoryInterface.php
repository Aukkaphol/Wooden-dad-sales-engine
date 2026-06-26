<?php

namespace App\Repositories\Contracts;

use App\Models\Brand;
use App\Models\Workspace;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BrandRepositoryInterface
{
    public function create(Workspace $workspace, array $attributes): Brand;

    public function update(Brand $brand, array $attributes): Brand;

    public function delete(Brand $brand): bool;

    public function paginateForWorkspace(Workspace $workspace, int $perPage = 15): LengthAwarePaginator;

    public function slugExists(Workspace $workspace, string $slug): bool;
}
