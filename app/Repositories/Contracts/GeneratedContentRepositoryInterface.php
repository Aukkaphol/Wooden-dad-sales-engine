<?php

namespace App\Repositories\Contracts;

use App\Models\GeneratedContent;
use App\Models\GeneratedContentVersion;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface GeneratedContentRepositoryInterface
{
    public function create(array $attributes): GeneratedContent;

    public function update(GeneratedContent $content, array $attributes): GeneratedContent;

    public function delete(GeneratedContent $content): bool;

    public function search(Workspace $workspace, array $filters, int $perPage = 15): LengthAwarePaginator;

    public function syncAssets(GeneratedContent $content, array $assetIds): void;

    public function createVersion(GeneratedContent $content, ?User $actor = null): GeneratedContentVersion;
}
