<?php

namespace Tests\Feature\Approvals;

use App\Jobs\LogActivityJob;
use App\Models\Brand;
use App\Models\GeneratedContent;
use App\Models\PromptTemplate;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ApprovalWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_creator_can_submit_draft_for_review_and_history_is_recorded(): void
    {
        Queue::fake();

        [$creator, $workspace, $content] = $this->contentFixture(WorkspaceUser::ROLE_CONTENT_CREATOR);

        $this->actingAs($creator)->post(route('workspaces.contents.workflow.submit', [$workspace, $content]), [
            'comment' => 'Ready for review.',
        ])->assertRedirect();

        $content->refresh();

        $this->assertSame(GeneratedContent::STATUS_IN_REVIEW, $content->status);
        $this->assertDatabaseHas('content_approval_histories', [
            'generated_content_id' => $content->getKey(),
            'reviewer_id' => $creator->getKey(),
            'decision' => 'submitted',
            'previous_status' => GeneratedContent::STATUS_DRAFT,
            'new_status' => GeneratedContent::STATUS_IN_REVIEW,
            'comment' => 'Ready for review.',
        ]);
        Queue::assertPushed(LogActivityJob::class);
    }

    public function test_reviewer_can_approve_reject_and_return_with_comment(): void
    {
        [$creator, $workspace, $content] = $this->contentFixture(WorkspaceUser::ROLE_CONTENT_CREATOR);
        $reviewer = $this->addUserToWorkspace($workspace, WorkspaceUser::ROLE_REVIEWER);

        $content->forceFill(['status' => GeneratedContent::STATUS_IN_REVIEW])->save();

        $this->actingAs($reviewer)->post(route('workspaces.contents.workflow.approve', [$workspace, $content]), [
            'reviewer_notes' => 'Looks good.',
        ])->assertRedirect();

        $this->assertSame(GeneratedContent::STATUS_APPROVED, $content->refresh()->status);
        $this->assertSame('Looks good.', $content->reviewer_notes);

        $this->actingAs($reviewer)->post(route('workspaces.contents.workflow.reject', [$workspace, $content]), [
            'comment' => 'Needs legal review.',
        ])->assertRedirect();

        $this->assertSame(GeneratedContent::STATUS_REJECTED, $content->refresh()->status);

        $content->forceFill(['status' => GeneratedContent::STATUS_IN_REVIEW])->save();

        $this->actingAs($reviewer)->post(route('workspaces.contents.workflow.return', [$workspace, $content]), [
            'comment' => 'Please revise CTA.',
        ])->assertRedirect();

        $this->assertSame(GeneratedContent::STATUS_DRAFT, $content->refresh()->status);
        $this->assertDatabaseHas('content_approval_histories', [
            'generated_content_id' => $content->getKey(),
            'decision' => 'returned',
            'comment' => 'Please revise CTA.',
        ]);
    }

    public function test_marketing_manager_can_schedule_and_publish_approved_content(): void
    {
        [$creator, $workspace, $content] = $this->contentFixture(WorkspaceUser::ROLE_CONTENT_CREATOR);
        $manager = $this->addUserToWorkspace($workspace, WorkspaceUser::ROLE_MARKETING_MANAGER);

        $content->forceFill(['status' => GeneratedContent::STATUS_APPROVED])->save();
        $scheduledAt = now()->addDay()->format('Y-m-d H:i:s');

        $this->actingAs($manager)->post(route('workspaces.contents.workflow.schedule', [$workspace, $content]), [
            'scheduled_at' => $scheduledAt,
            'comment' => 'Schedule for tomorrow.',
        ])->assertRedirect();

        $content->refresh();
        $this->assertSame(GeneratedContent::STATUS_SCHEDULED, $content->status);
        $this->assertNotNull($content->scheduled_at);

        $this->actingAs($manager)->post(route('workspaces.contents.workflow.publish', [$workspace, $content]), [
            'comment' => 'Published manually.',
        ])->assertRedirect();

        $content->refresh();
        $this->assertSame(GeneratedContent::STATUS_PUBLISHED, $content->status);
        $this->assertNotNull($content->published_at);
    }

    public function test_published_content_is_read_only_for_creator_and_admin(): void
    {
        [$creator, $workspace, $content, $brand, $prompt] = $this->contentFixture(WorkspaceUser::ROLE_CONTENT_CREATOR);
        $admin = $this->addUserToWorkspace($workspace, WorkspaceUser::ROLE_ADMIN);

        $content->forceFill(['status' => GeneratedContent::STATUS_PUBLISHED, 'published_at' => now()])->save();

        foreach ([$creator, $admin] as $actor) {
            $this->actingAs($actor)->put(route('workspaces.contents.update', [$workspace, $content]), [
                'brand_id' => $brand->getKey(),
                'prompt_template_id' => $prompt->getKey(),
                'title' => 'Read Only Edit',
                'platform' => 'facebook',
                'content_type' => GeneratedContent::TYPE_FACEBOOK_POST,
                'generated_content' => 'No edits allowed.',
                'status' => GeneratedContent::STATUS_DRAFT,
            ])->assertForbidden();
        }
    }

    public function test_non_reviewer_cannot_approve_and_owner_can_override(): void
    {
        [$creator, $workspace, $content] = $this->contentFixture(WorkspaceUser::ROLE_CONTENT_CREATOR);
        $member = $this->addUserToWorkspace($workspace, WorkspaceUser::ROLE_MEMBER);
        $owner = User::query()->findOrFail($workspace->owner_id);

        $content->forceFill(['status' => GeneratedContent::STATUS_IN_REVIEW])->save();

        $this->actingAs($member)->post(route('workspaces.contents.workflow.approve', [$workspace, $content]))
            ->assertForbidden();

        $this->actingAs($owner)->post(route('workspaces.contents.workflow.approve', [$workspace, $content]), [
            'comment' => 'Owner override.',
        ])->assertRedirect();

        $this->assertSame(GeneratedContent::STATUS_APPROVED, $content->refresh()->status);
    }

    public function test_admin_can_archive_content(): void
    {
        [$creator, $workspace, $content] = $this->contentFixture(WorkspaceUser::ROLE_CONTENT_CREATOR);
        $admin = $this->addUserToWorkspace($workspace, WorkspaceUser::ROLE_ADMIN);

        $this->actingAs($admin)->post(route('workspaces.contents.workflow.archive', [$workspace, $content]), [
            'comment' => 'No longer needed.',
        ])->assertRedirect();

        $this->assertSame(GeneratedContent::STATUS_ARCHIVED, $content->refresh()->status);
    }

    private function contentFixture(string $creatorRole): array
    {
        $owner = User::factory()->create();
        $creator = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $owner->getKey()]);

        WorkspaceUser::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'user_id' => $owner->getKey(),
            'role' => WorkspaceUser::ROLE_OWNER,
        ]);

        WorkspaceUser::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'user_id' => $creator->getKey(),
            'role' => $creatorRole,
        ]);

        $brand = Brand::factory()->create(['workspace_id' => $workspace->getKey()]);
        $prompt = PromptTemplate::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $brand->getKey(),
            'created_by' => $creator->getKey(),
        ]);
        $content = GeneratedContent::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $brand->getKey(),
            'prompt_template_id' => $prompt->getKey(),
            'created_by' => $creator->getKey(),
            'status' => GeneratedContent::STATUS_DRAFT,
        ]);

        return [$creator->refresh(), $workspace->refresh(), $content->refresh(), $brand->refresh(), $prompt->refresh()];
    }

    private function addUserToWorkspace(Workspace $workspace, string $role): User
    {
        $user = User::factory()->create();
        WorkspaceUser::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'user_id' => $user->getKey(),
            'role' => $role,
        ]);

        return $user->refresh();
    }
}
