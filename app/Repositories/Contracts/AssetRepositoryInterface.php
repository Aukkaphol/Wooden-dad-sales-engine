<?php

namespace App\Repositories\Contracts;

use App\Models\Asset;
use App\Models\Workspace;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AssetRepositoryInterface
{
    public function create(array $attributes): Asset;

    public function update(Asset $asset, array $attributes): Asset;

    public function delete(Asset $asset): bool;

    public function search(Workspace $workspace, array $filters, int $perPage = 15): LengthAwarePaginator;
}
