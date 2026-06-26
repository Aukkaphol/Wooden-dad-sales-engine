<?php

namespace App\Providers;

use App\Repositories\Contracts\ActivityLogRepositoryInterface;
use App\Repositories\Contracts\AiInsightRepositoryInterface;
use App\Repositories\Contracts\AnalyticsRecordRepositoryInterface;
use App\Repositories\Contracts\AssetRepositoryInterface;
use App\Repositories\Contracts\BrandRepositoryInterface;
use App\Repositories\Contracts\ContentApprovalRepositoryInterface;
use App\Repositories\Contracts\DashboardRepositoryInterface;
use App\Repositories\Contracts\GeneratedContentRepositoryInterface;
use App\Repositories\Contracts\MediaPipelineRepositoryInterface;
use App\Repositories\Contracts\PromptTemplateRepositoryInterface;
use App\Repositories\Contracts\PublishingQueueRepositoryInterface;
use App\Repositories\Contracts\PublishingJobRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\WorkspaceRepositoryInterface;
use App\Repositories\Eloquent\EloquentActivityLogRepository;
use App\Repositories\Eloquent\EloquentAiInsightRepository;
use App\Repositories\Eloquent\EloquentAnalyticsRecordRepository;
use App\Repositories\Eloquent\EloquentAssetRepository;
use App\Repositories\Eloquent\EloquentBrandRepository;
use App\Repositories\Eloquent\EloquentContentApprovalRepository;
use App\Repositories\Eloquent\EloquentDashboardRepository;
use App\Repositories\Eloquent\EloquentGeneratedContentRepository;
use App\Repositories\Eloquent\EloquentMediaPipelineRepository;
use App\Repositories\Eloquent\EloquentPromptTemplateRepository;
use App\Repositories\Eloquent\EloquentPublishingQueueRepository;
use App\Repositories\Eloquent\EloquentPublishingJobRepository;
use App\Repositories\Eloquent\EloquentUserRepository;
use App\Repositories\Eloquent\EloquentWorkspaceRepository;
use App\Social\Connectors\FacebookConnector;
use App\Social\Connectors\InstagramConnector;
use App\Social\Connectors\LineOaConnector;
use App\Social\Connectors\TikTokConnector;
use App\Social\Contracts\FacebookConnectorInterface;
use App\Social\Contracts\InstagramConnectorInterface;
use App\Social\Contracts\LineOaConnectorInterface;
use App\Social\Contracts\SocialConnectorRegistryInterface;
use App\Social\Contracts\TikTokConnectorInterface;
use App\Social\SocialConnectorRegistry;
use App\Models\ActivityLog;
use App\Models\AiInsight;
use App\Models\AnalyticsRecord;
use App\Models\Asset;
use App\Models\Brand;
use App\Models\GeneratedContent;
use App\Models\MediaPipelineRun;
use App\Models\PromptTemplate;
use App\Models\PublishingQueueItem;
use App\Models\User;
use App\Models\Workspace;
use App\Policies\ActivityLogPolicy;
use App\Policies\AiInsightPolicy;
use App\Policies\AnalyticsRecordPolicy;
use App\Policies\AssetPolicy;
use App\Policies\BrandPolicy;
use App\Policies\GeneratedContentPolicy;
use App\Policies\MediaPipelineRunPolicy;
use App\Policies\PromptTemplatePolicy;
use App\Policies\PublishingQueueItemPolicy;
use App\Policies\UserPolicy;
use App\Policies\WorkspacePolicy;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(StatefulGuard::class, fn () => Auth::guard('web'));
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->bind(ActivityLogRepositoryInterface::class, EloquentActivityLogRepository::class);
        $this->app->bind(WorkspaceRepositoryInterface::class, EloquentWorkspaceRepository::class);
        $this->app->bind(BrandRepositoryInterface::class, EloquentBrandRepository::class);
        $this->app->bind(AssetRepositoryInterface::class, EloquentAssetRepository::class);
        $this->app->bind(PromptTemplateRepositoryInterface::class, EloquentPromptTemplateRepository::class);
        $this->app->bind(GeneratedContentRepositoryInterface::class, EloquentGeneratedContentRepository::class);
        $this->app->bind(ContentApprovalRepositoryInterface::class, EloquentContentApprovalRepository::class);
        $this->app->bind(PublishingQueueRepositoryInterface::class, EloquentPublishingQueueRepository::class);
        $this->app->bind(AnalyticsRecordRepositoryInterface::class, EloquentAnalyticsRecordRepository::class);
        $this->app->bind(AiInsightRepositoryInterface::class, EloquentAiInsightRepository::class);
        $this->app->bind(DashboardRepositoryInterface::class, EloquentDashboardRepository::class);
        $this->app->bind(MediaPipelineRepositoryInterface::class, EloquentMediaPipelineRepository::class);
        $this->app->bind(PublishingJobRepositoryInterface::class, EloquentPublishingJobRepository::class);
        $this->app->bind(FacebookConnectorInterface::class, FacebookConnector::class);
        $this->app->bind(InstagramConnectorInterface::class, InstagramConnector::class);
        $this->app->bind(TikTokConnectorInterface::class, TikTokConnector::class);
        $this->app->bind(LineOaConnectorInterface::class, LineOaConnector::class);
        $this->app->singleton(SocialConnectorRegistryInterface::class, fn ($app) => new SocialConnectorRegistry($app, [
            'facebook' => FacebookConnector::class,
            'instagram' => InstagramConnector::class,
            'tiktok' => TikTokConnector::class,
            'line_oa' => LineOaConnector::class,
        ]));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(ActivityLog::class, ActivityLogPolicy::class);
        Gate::policy(Workspace::class, WorkspacePolicy::class);
        Gate::policy(Brand::class, BrandPolicy::class);
        Gate::policy(Asset::class, AssetPolicy::class);
        Gate::policy(PromptTemplate::class, PromptTemplatePolicy::class);
        Gate::policy(GeneratedContent::class, GeneratedContentPolicy::class);
        Gate::policy(PublishingQueueItem::class, PublishingQueueItemPolicy::class);
        Gate::policy(AnalyticsRecord::class, AnalyticsRecordPolicy::class);
        Gate::policy(AiInsight::class, AiInsightPolicy::class);
        Gate::policy(MediaPipelineRun::class, MediaPipelineRunPolicy::class);
    }
}
