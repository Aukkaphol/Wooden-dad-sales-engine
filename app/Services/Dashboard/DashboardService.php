<?php

namespace App\Services\Dashboard;

use App\Models\User;
use App\Repositories\Contracts\DashboardRepositoryInterface;
use App\Services\Director\AiDirectorService;
use Illuminate\Auth\Access\AuthorizationException;

class DashboardService
{
    public function __construct(
        private readonly DashboardRepositoryInterface $dashboard,
        private readonly AiDirectorService $director,
    ) {
    }

    public function data(User $user, array $filters): array
    {
        $workspaces = $this->dashboard->accessibleWorkspaces($user);
        $workspace = $this->dashboard->resolveWorkspace($user, $filters['workspace_id'] ?? null);

        if ($workspace === null) {
            throw new AuthorizationException('No accessible workspace is available.');
        }

        $brandId = $filters['brand_id'] ?? null;

        if ($brandId !== null && ! $workspace->brands->contains('id', $brandId)) {
            $brandId = null;
        }

        $filters['brand_id'] = $brandId;

        return [
            'workspaces' => $workspaces,
            'workspace' => $workspace,
            'filters' => $filters,
            'cards' => $this->dashboard->cards($user, $workspace, $filters),
            'activities' => $this->dashboard->activityTimeline($user, $workspace),
            'contents' => $this->dashboard->recentContents($workspace, $filters),
            'queueItems' => $this->dashboard->publishingQueue($workspace, $filters),
            'publishingStatusCounts' => $this->dashboard->publishingStatusCounts($workspace, $filters),
            'analyticsSummary' => $this->dashboard->analyticsSummary($workspace, $filters),
            'insights' => $this->dashboard->latestInsights($workspace, $filters),
            'directorWidgets' => $this->director->dashboardWidgets($workspace, $filters),
            'searchResults' => $this->dashboard->globalSearch($workspace, $filters),
        ];
    }
}
