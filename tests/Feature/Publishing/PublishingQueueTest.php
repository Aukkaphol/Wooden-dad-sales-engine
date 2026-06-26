<?php

namespace Tests\Feature\Publishing;

use App\Jobs\LogActivityJob;
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

class PublishingQueueTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_schedule_approved_content_and_history_is_recorded(): void
    {
        Queue::fake();

        [$manager, $workspace, $content] = $this->fixture(WorkspaceUser::ROLE_MARKETING_MANAGER, GeneratedContent::STATUS_APPROVED);
        $scheduledAt = now()->addDay()->format('Y-m-d H:i:s');

        $response = $this->actingAs($manager)->post(route('workspaces.publishing.store', $workspace), [
            'generated_content_id' => $content->getKey(),
            'platform' => 'facebook',
            'scheduled_at' => $scheduledAt,
            'priority' => 25,
            'comment' => 'Prepare for publish.',
        ]);

        $item = PublishingQueueItem::query()->where('generated_content_id', $content->getKey())->firstOrFail();

        $response->assertRedirect(route('workspaces.publishing.show', [$workspace, $item]));
        $this->assertSame(PublishingQueueItem::STATUS_SCHEDULED, $item->status);
        $this->assertSame(25, $item->priority);
        $this->assertSame(1, $item->histories()->count());
        $this->assertDatabaseHas('publishing_queue_histories', [
            'publishing_queue_item_id' => $item->getKey(),
            'event' => 'scheduled',
            'new_status' => PublishingQueueItem::STATUS_SCHEDULED,
            'comment' => 'Prepare for publish.',
        ]);
        Queue::assertPushed(LogActivityJob::class);
    }

    public function test_draft_content_cannot_enter_publishing_queue(): void
    {
        [$manager, $workspace, $content] = $this->fixture(WorkspaceUser::ROLE_MARKETING_MANAGER, GeneratedContent::STATUS_DRAFT);

        $this->actingAs($manager)->post(route('workspaces.publishing.store', $workspace), [
            'generated_content_id' => $content->getKey(),
            'platform' => 'facebook',
            'priority' => 100,
        ])->assertSessionHasErrors('generated_content_id');
    }

    public function test_manager_can_cancel_mark_failed_and_retry_failed_item(): void
    {
        Queue::fake();

        [$manager, $workspace, $content] = $this->fixture(WorkspaceUser::ROLE_MARKETING_MANAGER, GeneratedContent::STATUS_APPROVED);
        $item = PublishingQueueItem::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $content->brand_id,
            'generated_content_id' => $content->getKey(),
            'created_by' => $manager->getKey(),
            'status' => PublishingQueueItem::STATUS_WAITING,
        ]);

        $this->actingAs($manager)->post(route('workspaces.publishing.cancel', [$workspace, $item]), [
            'comment' => 'Pause.',
        ])->assertRedirect();
        $this->assertSame(PublishingQueueItem::STATUS_CANCELLED, $item->refresh()->status);

        $item->forceFill(['status' => PublishingQueueItem::STATUS_PROCESSING])->save();
        $this->actingAs($manager)->post(route('workspaces.publishing.failed', [$workspace, $item]), [
            'comment' => 'Renderer unavailable.',
        ])->assertRedirect();
        $this->assertSame(PublishingQueueItem::STATUS_FAILED, $item->refresh()->status);
        $this->assertSame('Renderer unavailable.', $item->failure_reason);

        $this->actingAs($manager)->post(route('workspaces.publishing.retry', [$workspace, $item]), [
            'comment' => 'Try again.',
        ])->assertRedirect();
        $item->refresh();
        $this->assertSame(PublishingQueueItem::STATUS_WAITING, $item->status);
        $this->assertSame(1, $item->retry_count);
        $this->assertNull($item->failure_reason);
        Queue::assertPushed(LogActivityJob::class);
    }

    public function test_manager_can_mark_processing_and_published(): void
    {
        [$manager, $workspace, $content] = $this->fixture(WorkspaceUser::ROLE_MARKETING_MANAGER, GeneratedContent::STATUS_APPROVED);
        $item = PublishingQueueItem::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $content->brand_id,
            'generated_content_id' => $content->getKey(),
            'created_by' => $manager->getKey(),
            'status' => PublishingQueueItem::STATUS_WAITING,
        ]);

        $this->actingAs($manager)->post(route('workspaces.publishing.processing', [$workspace, $item]))
            ->assertRedirect();
        $this->assertSame(PublishingQueueItem::STATUS_PROCESSING, $item->refresh()->status);

        $this->actingAs($manager)->post(route('workspaces.publishing.published', [$workspace, $item]))
            ->assertRedirect();
        $item->refresh();
        $this->assertSame(PublishingQueueItem::STATUS_PUBLISHED, $item->status);
        $this->assertNotNull($item->published_at);
    }

    public function test_search_and_filter_queue_items(): void
    {
        [$manager, $workspace, $content] = $this->fixture(WorkspaceUser::ROLE_MARKETING_MANAGER, GeneratedContent::STATUS_APPROVED);
        $otherBrand = Brand::factory()->create(['workspace_id' => $workspace->getKey()]);
        $otherContent = GeneratedContent::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $otherBrand->getKey(),
            'prompt_template_id' => PromptTemplate::factory()->create(['workspace_id' => $workspace->getKey(), 'brand_id' => $otherBrand->getKey(), 'created_by' => $manager->getKey()])->getKey(),
            'created_by' => $manager->getKey(),
            'title' => 'Other Draft',
        ]);

        PublishingQueueItem::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $content->brand_id,
            'generated_content_id' => $content->getKey(),
            'created_by' => $manager->getKey(),
            'platform' => 'facebook',
            'status' => PublishingQueueItem::STATUS_SCHEDULED,
        ]);
        PublishingQueueItem::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $otherBrand->getKey(),
            'generated_content_id' => $otherContent->getKey(),
            'created_by' => $manager->getKey(),
            'platform' => 'tiktok',
            'status' => PublishingQueueItem::STATUS_WAITING,
        ]);

        $this->actingAs($manager)->get(route('workspaces.publishing.index', [
            $workspace,
            'search' => $content->title,
            'brand_id' => $content->brand_id,
            'content_id' => $content->getKey(),
            'platform' => 'facebook',
            'status' => PublishingQueueItem::STATUS_SCHEDULED,
        ]))->assertOk()
            ->assertSee($content->title)
            ->assertDontSee('Other Draft');
    }

    public function test_member_cannot_schedule_or_manage_queue_item(): void
    {
        [$member, $workspace, $content] = $this->fixture(WorkspaceUser::ROLE_MEMBER, GeneratedContent::STATUS_APPROVED);
        $item = PublishingQueueItem::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $content->brand_id,
            'generated_content_id' => $content->getKey(),
            'created_by' => $member->getKey(),
            'status' => PublishingQueueItem::STATUS_WAITING,
        ]);

        $this->actingAs($member)->post(route('workspaces.publishing.store', $workspace), [
            'generated_content_id' => $content->getKey(),
            'platform' => 'facebook',
            'priority' => 100,
        ])->assertForbidden();

        $this->actingAs($member)->post(route('workspaces.publishing.cancel', [$workspace, $item]))
            ->assertForbidden();
    }

    public function test_queue_item_is_workspace_scoped(): void
    {
        [$manager, $workspace, $content] = $this->fixture(WorkspaceUser::ROLE_MARKETING_MANAGER, GeneratedContent::STATUS_APPROVED);
        $otherWorkspace = Workspace::factory()->create();
        $item = PublishingQueueItem::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $content->brand_id,
            'generated_content_id' => $content->getKey(),
            'created_by' => $manager->getKey(),
        ]);

        $this->actingAs($manager)->get(route('workspaces.publishing.show', [$otherWorkspace, $item]))
            ->assertNotFound();
    }

    private function fixture(string $role, string $contentStatus): array
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
        $prompt = PromptTemplate::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $brand->getKey(),
            'created_by' => $user->getKey(),
        ]);
        $content = GeneratedContent::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $brand->getKey(),
            'prompt_template_id' => $prompt->getKey(),
            'created_by' => $user->getKey(),
            'title' => 'Fleet Campaign Draft',
            'status' => $contentStatus,
        ]);

        return [$user->refresh(), $workspace->refresh(), $content->refresh()];
    }
}
