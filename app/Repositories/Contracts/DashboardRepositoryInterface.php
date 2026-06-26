<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface DashboardRepositoryInterface
{
    public function accessibleWorkspaces(User $user): Collection;

    public function resolveWorkspace(User $user, ?string $workspaceId): ?Workspace;

    public function cards(User $user, Workspace $workspace, array $filters): array;

    public function activityTimeline(User $user, Workspace $workspace, int $limit = 12): Collection;

    public function recentContents(Workspace $workspace, array $filters, int $perPage = 8): LengthAwarePaginator;

    public function publishingQueue(Workspace $workspace, array $filters, int $limit = 8): Collection;

    public function publishingStatusCounts(Workspace $workspace, array $filters): array;

    public function analyticsSummary(Workspace $workspace, array $filters): array;

    public function latestInsights(Workspace $workspace, array $filters, int $limit = 8): Collection;

    public function globalSearch(Workspace $workspace, array $filters, int $limit = 5): array;
}
