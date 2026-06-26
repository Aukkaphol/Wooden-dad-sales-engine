<?php

namespace App\Repositories\Eloquent;

use App\Models\ActivityLog;
use App\Models\AiInsight;
use App\Models\AnalyticsRecord;
use App\Models\Asset;
use App\Models\Brand;
use App\Models\GeneratedContent;
use App\Models\PromptTemplate;
use App\Models\PublishingQueueItem;
use App\Models\User;
use App\Models\Workspace;
use App\Repositories\Contracts\DashboardRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class EloquentDashboardRepository implements DashboardRepositoryInterface
{
    public function accessibleWorkspaces(User $user): Collection
    {
        return $user->workspaces()->with('brands')->orderBy('name')->get();
    }

    public function resolveWorkspace(User $user, ?string $workspaceId): ?Workspace
    {
        $query = $user->workspaces()->with('brands')->orderBy('name');

        if ($workspaceId !== null) {
            return $query->where('workspaces.id', $workspaceId)->first();
        }

        if ($user->current_workspace_id !== null) {
            $workspace = $query->where('workspaces.id', $user->current_workspace_id)->first();

            if ($workspace !== null) {
                return $workspace;
            }
        }

        return $this->accessibleWorkspaces($user)->first();
    }

    public function cards(User $user, Workspace $workspace, array $filters): array
    {
        $brandId = $filters['brand_id'] ?? null;

        return [
            'workspaces' => $this->card('Workspaces', Workspace::query()->whereIn('id', $user->workspaces()->pluck('workspaces.id'))),
            'brands' => $this->card('Brands', Brand::query()->where('workspace_id', $workspace->getKey())->when($brandId, fn ($query) => $query->whereKey($brandId))),
            'assets' => $this->card('Assets', $this->brandScoped(Asset::query()->where('workspace_id', $workspace->getKey()), $brandId)),
            'prompt_templates' => $this->card('Prompt Templates', $this->brandScoped(PromptTemplate::query()->where('workspace_id', $workspace->getKey()), $brandId)),
            'generated_contents' => $this->card('Generated Contents', $this->brandScoped(GeneratedContent::query()->where('workspace_id', $workspace->getKey()), $brandId)),
            'pending_approval' => $this->card('Pending Approval', $this->brandScoped(GeneratedContent::query()->where('workspace_id', $workspace->getKey())->where('status', GeneratedContent::STATUS_IN_REVIEW), $brandId)),
            'publishing_queue' => $this->card('Publishing Queue', $this->brandScoped(PublishingQueueItem::query()->where('workspace_id', $workspace->getKey()), $brandId)),
            'published_contents' => $this->card('Published Contents', $this->brandScoped(GeneratedContent::query()->where('workspace_id', $workspace->getKey())->where('status', GeneratedContent::STATUS_PUBLISHED), $brandId)),
            'analytics_records' => $this->card('Analytics Records', $this->brandScoped(AnalyticsRecord::query()->where('workspace_id', $workspace->getKey()), $brandId)),
            'ai_insights' => $this->card('AI Insights', $this->brandScoped(AiInsight::query()->where('workspace_id', $workspace->getKey()), $brandId)),
        ];
    }

    public function activityTimeline(User $user, Workspace $workspace, int $limit = 12): Collection
    {
        return ActivityLog::query()
            ->with('user')
            ->where('user_id', $user->getKey())
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function recentContents(Workspace $workspace, array $filters, int $perPage = 8): LengthAwarePaginator
    {
        $search = $filters['q'] ?? null;

        return $this->brandScoped(GeneratedContent::query()->where('workspace_id', $workspace->getKey()), $filters['brand_id'] ?? null)
            ->with(['brand', 'assets', 'analyticsRecords'])
            ->when($search, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('platform', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%");
                });
            })
            ->when($filters['content_status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->when($filters['platform'] ?? null, fn ($query, string $platform) => $query->where('platform', $platform))
            ->latest()
            ->paginate($perPage, ['*'], 'contents_page')
            ->withQueryString();
    }

    public function publishingQueue(Workspace $workspace, array $filters, int $limit = 8): Collection
    {
        return $this->brandScoped(PublishingQueueItem::query()->where('workspace_id', $workspace->getKey()), $filters['brand_id'] ?? null)
            ->with(['brand', 'generatedContent'])
            ->whereIn('status', [
                PublishingQueueItem::STATUS_SCHEDULED,
                PublishingQueueItem::STATUS_PROCESSING,
                PublishingQueueItem::STATUS_PUBLISHED,
                PublishingQueueItem::STATUS_FAILED,
            ])
            ->orderByRaw("CASE status WHEN 'failed' THEN 1 WHEN 'processing' THEN 2 WHEN 'scheduled' THEN 3 WHEN 'published' THEN 4 ELSE 5 END")
            ->orderBy('scheduled_at')
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function publishingStatusCounts(Workspace $workspace, array $filters): array
    {
        $query = $this->brandScoped(PublishingQueueItem::query()->where('workspace_id', $workspace->getKey()), $filters['brand_id'] ?? null);

        return [
            PublishingQueueItem::STATUS_SCHEDULED => (clone $query)->where('status', PublishingQueueItem::STATUS_SCHEDULED)->count(),
            PublishingQueueItem::STATUS_PROCESSING => (clone $query)->where('status', PublishingQueueItem::STATUS_PROCESSING)->count(),
            PublishingQueueItem::STATUS_PUBLISHED => (clone $query)->where('status', PublishingQueueItem::STATUS_PUBLISHED)->count(),
            PublishingQueueItem::STATUS_FAILED => (clone $query)->where('status', PublishingQueueItem::STATUS_FAILED)->count(),
        ];
    }

    public function analyticsSummary(Workspace $workspace, array $filters): array
    {
        $query = $this->brandScoped(AnalyticsRecord::query()->where('workspace_id', $workspace->getKey()), $filters['brand_id'] ?? null);

        return [
            'views' => (int) (clone $query)->sum('views'),
            'reach' => (int) (clone $query)->sum('reach'),
            'engagement' => (int) (clone $query)->selectRaw('COALESCE(SUM(likes + comments + shares + saves), 0) as aggregate')->value('aggregate'),
            'followers_gained' => (int) (clone $query)->sum('follows_gained'),
        ];
    }

    public function latestInsights(Workspace $workspace, array $filters, int $limit = 8): Collection
    {
        return $this->brandScoped(AiInsight::query()->where('workspace_id', $workspace->getKey()), $filters['brand_id'] ?? null)
            ->with(['brand', 'generatedContent'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function globalSearch(Workspace $workspace, array $filters, int $limit = 5): array
    {
        $search = $filters['q'] ?? null;
        $brandId = $filters['brand_id'] ?? null;

        if (! $search) {
            return [];
        }

        return [
            'Assets' => $this->brandScoped(Asset::query()->where('workspace_id', $workspace->getKey()), $brandId)
                ->where(fn ($query) => $query->where('name', 'like', "%{$search}%")->orWhere('category', 'like', "%{$search}%"))
                ->latest()
                ->limit($limit)
                ->get(),
            'Prompts' => $this->brandScoped(PromptTemplate::query()->where('workspace_id', $workspace->getKey()), $brandId)
                ->where(fn ($query) => $query->where('title', 'like', "%{$search}%")->orWhere('prompt_template', 'like', "%{$search}%"))
                ->latest()
                ->limit($limit)
                ->get(),
            'Generated Content' => $this->brandScoped(GeneratedContent::query()->where('workspace_id', $workspace->getKey()), $brandId)
                ->where(fn ($query) => $query->where('title', 'like', "%{$search}%")->orWhere('generated_content', 'like', "%{$search}%"))
                ->latest()
                ->limit($limit)
                ->get(),
            'Publishing Queue' => $this->brandScoped(PublishingQueueItem::query()->where('workspace_id', $workspace->getKey()), $brandId)
                ->where(fn ($query) => $query->where('platform', 'like', "%{$search}%")->orWhere('status', 'like', "%{$search}%")->orWhere('failure_reason', 'like', "%{$search}%"))
                ->latest()
                ->limit($limit)
                ->get(),
            'Analytics' => $this->brandScoped(AnalyticsRecord::query()->where('workspace_id', $workspace->getKey()), $brandId)
                ->where(fn ($query) => $query->where('platform', 'like', "%{$search}%")->orWhere('notes', 'like', "%{$search}%"))
                ->latest()
                ->limit($limit)
                ->get(),
            'AI Insights' => $this->brandScoped(AiInsight::query()->where('workspace_id', $workspace->getKey()), $brandId)
                ->where(fn ($query) => $query->where('title', 'like', "%{$search}%")->orWhere('recommendation', 'like', "%{$search}%"))
                ->latest()
                ->limit($limit)
                ->get(),
        ];
    }

    private function card(string $label, Builder $query): array
    {
        return [
            'label' => $label,
            'total' => (clone $query)->count(),
            'today' => (clone $query)->whereDate('created_at', today())->count(),
            'week' => (clone $query)->where('created_at', '>=', now()->startOfWeek())->count(),
        ];
    }

    private function brandScoped(Builder $query, ?string $brandId): Builder
    {
        return $query->when($brandId, fn ($query) => $query->where('brand_id', $brandId));
    }
}
