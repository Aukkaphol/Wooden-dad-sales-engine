<?php

namespace Tests\Feature\Pipeline;

use App\Jobs\LogActivityJob;
use App\Models\AiInsight;
use App\Models\AnalyticsRecord;
use App\Models\Asset;
use App\Models\Brand;
use App\Models\GeneratedContent;
use App\Models\MediaPipelineHistory;
use App\Models\MediaPipelineRun;
use App\Models\PromptTemplate;
use App\Models\PublishingQueueItem;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class MediaPipelineTest extends TestCase
{
    use RefreshDatabase;

    public function test_asset_to_prompt_selection_creates_pipeline_history(): void
    {
        Queue::fake();

        [$user, $workspace, $brand, $asset, $prompt] = $this->fixture();

        $this->actingAs($user)->post(route('workspaces.pipeline.store', $workspace), $this->startPayload($brand, $asset, $prompt))
            ->assertRedirect();

        $pipeline = MediaPipelineRun::query()->firstOrFail();

        $this->assertSame([$asset->getKey()], $pipeline->asset_ids);
        $this->assertSame($prompt->getKey(), $pipeline->prompt_template_id);
        $this->assertDatabaseHas('media_pipeline_histories', [
            'media_pipeline_run_id' => $pipeline->getKey(),
            'event' => MediaPipelineHistory::EVENT_ASSETS_SELECTED,
        ]);
        $this->assertDatabaseHas('media_pipeline_histories', [
            'media_pipeline_run_id' => $pipeline->getKey(),
            'event' => MediaPipelineHistory::EVENT_PROMPT_SELECTED,
        ]);
        Queue::assertPushed(LogActivityJob::class);
    }

    public function test_prompt_to_content_creates_generated_content_pending_approval(): void
    {
        [$user, $workspace, $brand, $asset, $prompt] = $this->fixture();

        $this->actingAs($user)->post(route('workspaces.pipeline.store', $workspace), $this->startPayload($brand, $asset, $prompt));

        $pipeline = MediaPipelineRun::query()->firstOrFail();
        $content = $pipeline->generatedContent;

        $this->assertNotNull($content);
        $this->assertSame(GeneratedContent::STATUS_IN_REVIEW, $content->status);
        $this->assertTrue($content->assets()->whereKey($asset->getKey())->exists());
        $this->assertSame(MediaPipelineRun::STATUS_PENDING_APPROVAL, $pipeline->status);
    }

    public function test_content_approval_actions_update_pipeline(): void
    {
        [$user, $workspace, $brand, $asset, $prompt] = $this->fixture();
        $pipeline = $this->startedPipeline($user, $workspace, $brand, $asset, $prompt);

        $this->actingAs($user)->post(route('workspaces.pipeline.approve', [$workspace, $pipeline]))
            ->assertRedirect();

        $pipeline->refresh();
        $this->assertSame(MediaPipelineRun::STATUS_APPROVED, $pipeline->status);
        $this->assertSame(GeneratedContent::STATUS_APPROVED, $pipeline->generatedContent->status);
        $this->assertDatabaseHas('media_pipeline_histories', [
            'media_pipeline_run_id' => $pipeline->getKey(),
            'event' => MediaPipelineHistory::EVENT_APPROVED,
        ]);
    }

    public function test_reject_and_revision_actions_are_supported(): void
    {
        [$user, $workspace, $brand, $asset, $prompt] = $this->fixture();
        $pipeline = $this->startedPipeline($user, $workspace, $brand, $asset, $prompt);

        $this->actingAs($user)->post(route('workspaces.pipeline.reject', [$workspace, $pipeline]))
            ->assertRedirect();
        $this->assertSame(MediaPipelineRun::STATUS_REJECTED, $pipeline->refresh()->status);

        $this->actingAs($user)->post(route('workspaces.pipeline.revision', [$workspace, $pipeline]), [
            'comment' => 'Revise the hook.',
        ])->assertRedirect();

        $pipeline->refresh();
        $this->assertSame(MediaPipelineRun::STATUS_REVISION_REQUESTED, $pipeline->status);
        $this->assertSame(GeneratedContent::STATUS_DRAFT, $pipeline->generatedContent->status);
    }

    public function test_approval_to_queue_creates_publishing_queue_item(): void
    {
        [$user, $workspace, $brand, $asset, $prompt] = $this->fixture();
        $pipeline = $this->approvedPipeline($user, $workspace, $brand, $asset, $prompt);

        $this->actingAs($user)->post(route('workspaces.pipeline.queue', [$workspace, $pipeline]), [
            'platform' => 'facebook',
            'priority' => 50,
        ])->assertRedirect();

        $pipeline->refresh();
        $this->assertSame(MediaPipelineRun::STATUS_QUEUED, $pipeline->status);
        $this->assertNotNull($pipeline->publishing_queue_item_id);
        $this->assertSame(PublishingQueueItem::STATUS_WAITING, $pipeline->publishingQueueItem->status);
    }

    public function test_queue_to_analytics_creates_empty_analytics_after_publish(): void
    {
        [$user, $workspace, $brand, $asset, $prompt] = $this->fixture();
        $pipeline = $this->queuedPipeline($user, $workspace, $brand, $asset, $prompt);

        $this->actingAs($user)->post(route('workspaces.pipeline.publish', [$workspace, $pipeline]))
            ->assertRedirect();

        $pipeline->refresh();
        $this->assertSame(MediaPipelineRun::STATUS_PUBLISHED, $pipeline->status);
        $this->assertNotNull($pipeline->analytics_record_id);
        $this->assertSame(0, $pipeline->analyticsRecord->views);
        $this->assertSame(PublishingQueueItem::STATUS_PUBLISHED, $pipeline->publishingQueueItem->status);
    }

    public function test_analytics_update_refreshes_rule_based_insight(): void
    {
        [$user, $workspace, $brand, $asset, $prompt] = $this->fixture();
        $pipeline = $this->publishedPipeline($user, $workspace, $brand, $asset, $prompt);

        $this->actingAs($user)->patch(route('workspaces.pipeline.analytics', [$workspace, $pipeline]), [
            'views' => 2000,
            'reach' => 1500,
            'impressions' => 2500,
            'likes' => 120,
            'comments' => 20,
            'shares' => 18,
            'saves' => 10,
            'follows_gained' => 6,
            'link_clicks' => 30,
        ])->assertRedirect();

        $pipeline->refresh();
        $this->assertSame(MediaPipelineRun::STATUS_INSIGHT_CREATED, $pipeline->status);
        $this->assertNotNull($pipeline->ai_insight_id);
        $this->assertSame(2000, $pipeline->analyticsRecord->views);
        $this->assertSame(1, AiInsight::query()->where('analytics_record_id', $pipeline->analytics_record_id)->count());
    }

    public function test_end_to_end_pipeline(): void
    {
        [$user, $workspace, $brand, $asset, $prompt] = $this->fixture();
        $pipeline = $this->publishedPipeline($user, $workspace, $brand, $asset, $prompt);

        $this->actingAs($user)->patch(route('workspaces.pipeline.analytics', [$workspace, $pipeline]), [
            'views' => 900,
            'reach' => 700,
            'impressions' => 1000,
            'likes' => 40,
            'comments' => 8,
            'shares' => 7,
            'saves' => 4,
            'follows_gained' => 2,
            'link_clicks' => 9,
        ])->assertRedirect();

        $pipeline->refresh();
        $this->assertSame(MediaPipelineRun::STAGE_INSIGHTS, $pipeline->current_stage);
        $this->assertSame(MediaPipelineRun::STATUS_INSIGHT_CREATED, $pipeline->status);
        $this->assertGreaterThanOrEqual(9, $pipeline->histories()->count());
    }

    public function test_pipeline_page_loads_and_is_authorized(): void
    {
        [$user, $workspace] = $this->fixture();
        $outsider = User::factory()->create();

        $this->actingAs($user)->get(route('workspaces.pipeline.index', $workspace))
            ->assertOk()
            ->assertSee('Media Pipeline');

        $this->actingAs($outsider)->get(route('workspaces.pipeline.index', $workspace))
            ->assertForbidden();
    }

    private function startedPipeline(User $user, Workspace $workspace, Brand $brand, Asset $asset, PromptTemplate $prompt): MediaPipelineRun
    {
        $this->actingAs($user)->post(route('workspaces.pipeline.store', $workspace), $this->startPayload($brand, $asset, $prompt));

        return MediaPipelineRun::query()->firstOrFail();
    }

    private function approvedPipeline(User $user, Workspace $workspace, Brand $brand, Asset $asset, PromptTemplate $prompt): MediaPipelineRun
    {
        $pipeline = $this->startedPipeline($user, $workspace, $brand, $asset, $prompt);
        $this->actingAs($user)->post(route('workspaces.pipeline.approve', [$workspace, $pipeline]));

        return $pipeline->refresh();
    }

    private function queuedPipeline(User $user, Workspace $workspace, Brand $brand, Asset $asset, PromptTemplate $prompt): MediaPipelineRun
    {
        $pipeline = $this->approvedPipeline($user, $workspace, $brand, $asset, $prompt);
        $this->actingAs($user)->post(route('workspaces.pipeline.queue', [$workspace, $pipeline]), [
            'platform' => 'facebook',
            'priority' => 100,
        ]);

        return $pipeline->refresh();
    }

    private function publishedPipeline(User $user, Workspace $workspace, Brand $brand, Asset $asset, PromptTemplate $prompt): MediaPipelineRun
    {
        $pipeline = $this->queuedPipeline($user, $workspace, $brand, $asset, $prompt);
        $this->actingAs($user)->post(route('workspaces.pipeline.publish', [$workspace, $pipeline]));

        return $pipeline->refresh();
    }

    private function fixture(): array
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->getKey()]);
        WorkspaceUser::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'user_id' => $user->getKey(),
            'role' => WorkspaceUser::ROLE_OWNER,
        ]);
        $brand = Brand::factory()->create(['workspace_id' => $workspace->getKey(), 'name' => 'QuickTruck']);
        $asset = Asset::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $brand->getKey(),
            'uploaded_by' => $user->getKey(),
            'name' => 'Campaign Asset',
        ]);
        $prompt = PromptTemplate::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $brand->getKey(),
            'created_by' => $user->getKey(),
            'title' => 'Campaign Prompt',
            'prompt_template' => 'Create a post about {{ topic }}.',
            'version' => 3,
        ]);

        return [$user->refresh(), $workspace->refresh(), $brand->refresh(), $asset->refresh(), $prompt->refresh()];
    }

    private function startPayload(Brand $brand, Asset $asset, PromptTemplate $prompt): array
    {
        return [
            'brand_id' => $brand->getKey(),
            'asset_ids' => [$asset->getKey()],
            'prompt_template_id' => $prompt->getKey(),
            'title' => 'Pipeline Campaign',
            'platform' => 'facebook',
            'content_type' => GeneratedContent::TYPE_FACEBOOK_POST,
            'variables' => ['topic' => 'fleet launch'],
        ];
    }
}
