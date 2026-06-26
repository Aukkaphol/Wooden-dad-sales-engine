<?php

namespace Tests\Feature\Analytics;

use App\Jobs\LogActivityJob;
use App\Models\AiInsight;
use App\Models\AnalyticsRecord;
use App\Models\Brand;
use App\Models\GeneratedContent;
use App\Models\PromptTemplate;
use App\Models\PublishingQueueItem;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AnalyticsLiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_create_analytics_record_and_auto_insight(): void
    {
        Queue::fake();

        [$manager, $workspace, $content] = $this->fixture(WorkspaceUser::ROLE_MARKETING_MANAGER);
        $queueItem = PublishingQueueItem::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $content->brand_id,
            'generated_content_id' => $content->getKey(),
            'created_by' => $manager->getKey(),
        ]);

        $response = $this->actingAs($manager)->post(route('workspaces.analytics.store', $workspace), $this->analyticsPayload($content, [
            'publishing_queue_item_id' => $queueItem->getKey(),
            'views' => 2400,
            'likes' => 110,
            'comments' => 24,
            'shares' => 18,
            'saves' => 14,
            'follows_gained' => 9,
            'link_clicks' => 40,
        ]));

        $record = AnalyticsRecord::query()->where('generated_content_id', $content->getKey())->firstOrFail();

        $response->assertRedirect(route('workspaces.analytics.show', [$workspace, $record]));
        $this->assertSame($workspace->getKey(), $record->workspace_id);
        $this->assertSame($content->brand_id, $record->brand_id);
        $this->assertGreaterThan(0, $record->score);
        $this->assertSame(1, AiInsight::query()->where('analytics_record_id', $record->getKey())->count());
        Queue::assertPushed(LogActivityJob::class);
    }

    public function test_manager_can_update_analytics_record(): void
    {
        [$manager, $workspace, $content] = $this->fixture(WorkspaceUser::ROLE_MARKETING_MANAGER);
        $record = AnalyticsRecord::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $content->brand_id,
            'generated_content_id' => $content->getKey(),
            'created_by' => $manager->getKey(),
            'views' => 100,
        ]);

        $this->actingAs($manager)->put(route('workspaces.analytics.update', [$workspace, $record]), $this->analyticsPayload($content, [
            'views' => 5000,
            'likes' => 300,
            'notes' => 'Updated performance snapshot.',
        ]))->assertRedirect(route('workspaces.analytics.show', [$workspace, $record]));

        $record->refresh();
        $this->assertSame(5000, $record->views);
        $this->assertSame('Updated performance snapshot.', $record->notes);
        $this->assertGreaterThan(0, $record->score);
    }

    public function test_manager_can_delete_analytics_record(): void
    {
        [$manager, $workspace, $content] = $this->fixture(WorkspaceUser::ROLE_MARKETING_MANAGER);
        $record = AnalyticsRecord::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $content->brand_id,
            'generated_content_id' => $content->getKey(),
            'created_by' => $manager->getKey(),
        ]);

        $this->actingAs($manager)->delete(route('workspaces.analytics.destroy', [$workspace, $record]))
            ->assertRedirect(route('workspaces.analytics.index', $workspace));

        $this->assertSoftDeleted('analytics_records', ['id' => $record->getKey()]);
    }

    public function test_member_can_view_and_filter_analytics_list(): void
    {
        [$member, $workspace, $content] = $this->fixture(WorkspaceUser::ROLE_MEMBER);
        $otherBrand = Brand::factory()->create(['workspace_id' => $workspace->getKey()]);
        $otherContent = $this->contentFor($workspace, $otherBrand, $member, 'Hidden Draft', GeneratedContent::TYPE_TIKTOK_SCRIPT);

        AnalyticsRecord::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $content->brand_id,
            'generated_content_id' => $content->getKey(),
            'platform' => 'facebook',
            'notes' => 'Winning campaign',
            'captured_at' => now(),
        ]);
        AnalyticsRecord::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $otherBrand->getKey(),
            'generated_content_id' => $otherContent->getKey(),
            'platform' => 'tiktok',
            'notes' => 'Different campaign',
            'captured_at' => now(),
        ]);

        $this->actingAs($member)->get(route('workspaces.analytics.index', [
            $workspace,
            'search' => 'Winning',
            'brand_id' => $content->brand_id,
            'platform' => 'facebook',
            'content_type' => GeneratedContent::TYPE_FACEBOOK_POST,
        ]))->assertOk()
            ->assertSee('Fleet Campaign Draft')
            ->assertDontSee('Hidden Draft');
    }

    public function test_manager_can_create_manual_ai_insight(): void
    {
        [$manager, $workspace, $content] = $this->fixture(WorkspaceUser::ROLE_MARKETING_MANAGER);

        $response = $this->actingAs($manager)->post(route('workspaces.insights.store', $workspace), [
            'generated_content_id' => $content->getKey(),
            'insight_type' => AiInsight::TYPE_AUDIENCE_INSIGHT,
            'title' => 'Women 25-44 are strongest',
            'summary' => 'Manual review found the strongest audience segment.',
            'recommendation' => 'Create a shorter Reel version for evening posting.',
            'priority' => AiInsight::PRIORITY_HIGH,
            'metadata' => ['source' => 'manual'],
        ]);

        $insight = AiInsight::query()->where('title', 'Women 25-44 are strongest')->firstOrFail();

        $response->assertRedirect(route('workspaces.insights.show', [$workspace, $insight]));
        $this->assertSame($content->brand_id, $insight->brand_id);
        $this->assertSame(AiInsight::STATUS_NEW, $insight->status);
    }

    public function test_manager_can_update_ai_insight_status(): void
    {
        [$manager, $workspace, $content] = $this->fixture(WorkspaceUser::ROLE_MARKETING_MANAGER);
        $insight = AiInsight::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $content->brand_id,
            'generated_content_id' => $content->getKey(),
            'created_by' => $manager->getKey(),
            'status' => AiInsight::STATUS_NEW,
        ]);

        $this->actingAs($manager)->patch(route('workspaces.insights.status', [$workspace, $insight]), [
            'status' => AiInsight::STATUS_APPLIED,
        ])->assertRedirect();

        $this->assertSame(AiInsight::STATUS_APPLIED, $insight->refresh()->status);
    }

    public function test_policy_access_is_scoped_by_workspace_and_brand(): void
    {
        [$member, $workspace, $content] = $this->fixture(WorkspaceUser::ROLE_MEMBER);
        $record = AnalyticsRecord::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $content->brand_id,
            'generated_content_id' => $content->getKey(),
        ]);
        $otherWorkspace = Workspace::factory()->create();

        $this->actingAs($member)->post(route('workspaces.analytics.store', $workspace), $this->analyticsPayload($content))
            ->assertForbidden();

        $this->actingAs($member)->get(route('workspaces.analytics.show', [$otherWorkspace, $record]))
            ->assertNotFound();
    }

    private function fixture(string $role): array
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create([
            'owner_id' => $role === WorkspaceUser::ROLE_OWNER ? $user->getKey() : User::factory(),
        ]);
        WorkspaceUser::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'user_id' => $user->getKey(),
            'role' => $role,
        ]);
        $brand = Brand::factory()->create(['workspace_id' => $workspace->getKey()]);
        $content = $this->contentFor($workspace, $brand, $user, 'Fleet Campaign Draft', GeneratedContent::TYPE_FACEBOOK_POST);

        return [$user->refresh(), $workspace->refresh(), $content->refresh()];
    }

    private function contentFor(Workspace $workspace, Brand $brand, User $user, string $title, string $type): GeneratedContent
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
            'platform' => 'facebook',
            'content_type' => $type,
        ]);
    }

    private function analyticsPayload(GeneratedContent $content, array $overrides = []): array
    {
        return array_merge([
            'generated_content_id' => $content->getKey(),
            'platform' => 'facebook',
            'posted_at' => now()->subDay()->format('Y-m-d H:i:s'),
            'captured_at' => now()->format('Y-m-d H:i:s'),
            'views' => 1000,
            'reach' => 800,
            'impressions' => 1200,
            'likes' => 40,
            'comments' => 6,
            'shares' => 5,
            'saves' => 4,
            'follows_gained' => 2,
            'link_clicks' => 12,
            'estimated_revenue' => 120,
            'cost' => 30,
            'notes' => 'Manual analytics capture.',
            'audience_breakdown' => [
                'gender' => ['female' => 73, 'male' => 26, 'unknown' => 1],
                'age' => ['18-24' => 17.9, '25-34' => 32.1, '35-44' => 29.8],
                'source' => ['feed' => 64, 'page' => 26, 'other' => 10],
                'followers' => ['followers' => 61, 'non_followers' => 39],
            ],
            'metadata' => ['entry_mode' => 'manual'],
        ], $overrides);
    }
}
