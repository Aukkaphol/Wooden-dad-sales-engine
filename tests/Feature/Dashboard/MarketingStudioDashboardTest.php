<?php

namespace Tests\Feature\Dashboard;

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
use App\Models\WorkspaceUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketingStudioDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_loads_for_workspace_member(): void
    {
        [$user, $workspace, $brand, $content] = $this->fixture();
        Asset::factory()->create(['workspace_id' => $workspace->getKey(), 'brand_id' => $brand->getKey(), 'uploaded_by' => $user->getKey(), 'name' => 'Dashboard Hero']);
        PromptTemplate::factory()->create(['workspace_id' => $workspace->getKey(), 'brand_id' => $brand->getKey(), 'created_by' => $user->getKey(), 'title' => 'Launch Prompt']);
        PublishingQueueItem::factory()->create(['workspace_id' => $workspace->getKey(), 'brand_id' => $brand->getKey(), 'generated_content_id' => $content->getKey(), 'created_by' => $user->getKey(), 'status' => PublishingQueueItem::STATUS_SCHEDULED]);
        AnalyticsRecord::factory()->create(['workspace_id' => $workspace->getKey(), 'brand_id' => $brand->getKey(), 'generated_content_id' => $content->getKey(), 'views' => 1200]);
        AiInsight::factory()->create(['workspace_id' => $workspace->getKey(), 'brand_id' => $brand->getKey(), 'generated_content_id' => $content->getKey(), 'title' => 'Improve the hook']);

        $this->actingAs($user)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Studio Dashboard')
            ->assertSee('Workspaces')
            ->assertSee('Brands')
            ->assertSee('Assets')
            ->assertSee('Prompt Templates')
            ->assertSee('Generated Contents')
            ->assertSee('Pending Approval')
            ->assertSee('Publishing Queue')
            ->assertSee('Analytics Records')
            ->assertSee('AI Insights')
            ->assertSee('Fleet Campaign Draft')
            ->assertSee('Improve the hook')
            ->assertSee('1,200');
    }

    public function test_workspace_filtering_refreshes_dashboard_widgets(): void
    {
        [$user, $workspace, , $content] = $this->fixture();
        [$otherWorkspace, $otherContent] = $this->secondWorkspace($user);

        $this->actingAs($user)->get(route('dashboard', ['workspace_id' => $workspace->getKey()]))
            ->assertOk()
            ->assertSee($content->title)
            ->assertDontSee($otherContent->title);

        $this->actingAs($user)->get(route('dashboard', ['workspace_id' => $otherWorkspace->getKey()]))
            ->assertOk()
            ->assertSee($otherContent->title)
            ->assertDontSee($content->title);
    }

    public function test_brand_filtering_limits_recent_content(): void
    {
        [$user, $workspace, $brand, $content] = $this->fixture();
        $otherBrand = Brand::factory()->create(['workspace_id' => $workspace->getKey(), 'name' => 'QuickTruck Parts']);
        $otherContent = $this->contentFor($workspace, $otherBrand, $user, 'Parts Promo');

        $this->actingAs($user)->get(route('dashboard', [
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $brand->getKey(),
        ]))->assertOk()
            ->assertSee($content->title)
            ->assertDontSee($otherContent->title);
    }

    public function test_global_search_returns_matches_across_modules(): void
    {
        [$user, $workspace, $brand, $content] = $this->fixture();
        Asset::factory()->create(['workspace_id' => $workspace->getKey(), 'brand_id' => $brand->getKey(), 'uploaded_by' => $user->getKey(), 'name' => 'Summit Asset']);
        PromptTemplate::factory()->create(['workspace_id' => $workspace->getKey(), 'brand_id' => $brand->getKey(), 'created_by' => $user->getKey(), 'title' => 'Summit Prompt']);
        AiInsight::factory()->create(['workspace_id' => $workspace->getKey(), 'brand_id' => $brand->getKey(), 'generated_content_id' => $content->getKey(), 'title' => 'Summit Insight']);

        $this->actingAs($user)->get(route('dashboard', [
            'workspace_id' => $workspace->getKey(),
            'q' => 'Summit',
        ]))->assertOk()
            ->assertSee('Global search')
            ->assertSee('Summit Asset')
            ->assertSee('Summit Prompt')
            ->assertSee('Summit Insight');
    }

    public function test_activity_timeline_shows_latest_activity(): void
    {
        [$user] = $this->fixture();
        ActivityLog::factory()->create([
            'user_id' => $user->getKey(),
            'event' => 'analytics.created',
            'description' => 'Analytics added',
            'created_at' => now(),
        ]);

        $this->actingAs($user)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Activity Timeline')
            ->assertSee('Analytics added');
    }

    public function test_dashboard_requires_accessible_workspace(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));

        $user = User::factory()->create();

        $this->actingAs($user)->get(route('dashboard'))
            ->assertRedirect(route('onboarding.workspace.create'));
    }

    private function fixture(): array
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->getKey(), 'name' => 'QuickTruck']);
        WorkspaceUser::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'user_id' => $user->getKey(),
            'role' => WorkspaceUser::ROLE_OWNER,
        ]);
        $user->forceFill(['current_workspace_id' => $workspace->getKey()])->save();
        $brand = Brand::factory()->create(['workspace_id' => $workspace->getKey(), 'name' => 'QuickTruck Fleet']);
        $content = $this->contentFor($workspace, $brand, $user, 'Fleet Campaign Draft');

        return [$user->refresh(), $workspace->refresh(), $brand->refresh(), $content->refresh()];
    }

    private function secondWorkspace(User $user): array
    {
        $workspace = Workspace::factory()->create(['owner_id' => $user->getKey(), 'name' => 'Wooden Dad']);
        WorkspaceUser::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'user_id' => $user->getKey(),
            'role' => WorkspaceUser::ROLE_OWNER,
        ]);
        $brand = Brand::factory()->create(['workspace_id' => $workspace->getKey(), 'name' => 'Wooden Dad Design']);
        $content = $this->contentFor($workspace, $brand, $user, 'Wooden Dad Campaign');

        return [$workspace->refresh(), $content->refresh()];
    }

    private function contentFor(Workspace $workspace, Brand $brand, User $user, string $title): GeneratedContent
    {
        $prompt = PromptTemplate::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $brand->getKey(),
            'created_by' => $user->getKey(),
        ]);

        return GeneratedContent::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $brand->getKey(),
            'prompt_template_id' => $prompt->getKey(),
            'created_by' => $user->getKey(),
            'title' => $title,
        ]);
    }
}
