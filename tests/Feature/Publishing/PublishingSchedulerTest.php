<?php

namespace Tests\Feature\Publishing;

use App\Enums\SocialPlatform;
use App\Jobs\ProcessPublishingJob;
use App\Models\Brand;
use App\Models\GeneratedContent;
use App\Models\PromptTemplate;
use App\Models\PublishingJob;
use App\Models\PublishingQueueItem;
use App\Models\SocialAccount;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceUser;
use App\Services\Publishing\PublishingJobService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class PublishingSchedulerTest extends TestCase
{
    use RefreshDatabase;

    public function test_publish_now_creates_queued_job_and_dispatches_laravel_queue_job(): void
    {
        Queue::fake();

        [$user, $workspace, $item, $account] = $this->fixture();

        $this->actingAs($user)->post(route('workspaces.publishing.jobs.publish-now', [$workspace, $item]), [
            'social_account_id' => $account->getKey(),
        ])->assertRedirect();

        $job = PublishingJob::query()->firstOrFail();

        $this->assertSame(PublishingJob::STATUS_QUEUED, $job->status);
        $this->assertSame($item->getKey(), $job->publishing_queue_item_id);
        $this->assertSame($account->getKey(), $job->social_account_id);
        $this->assertDatabaseHas('publishing_logs', [
            'publishing_job_id' => $job->getKey(),
            'event' => 'created',
        ]);
        Queue::assertPushed(ProcessPublishingJob::class);
    }

    public function test_schedule_publishing_creates_scheduled_job(): void
    {
        Queue::fake();

        [$user, $workspace, $item, $account] = $this->fixture();
        $scheduledAt = now()->addHour()->format('Y-m-d H:i:s');

        $this->actingAs($user)->post(route('workspaces.publishing.jobs.schedule', [$workspace, $item]), [
            'social_account_id' => $account->getKey(),
            'scheduled_at' => $scheduledAt,
        ])->assertRedirect();

        $job = PublishingJob::query()->firstOrFail();

        $this->assertSame(PublishingJob::STATUS_SCHEDULED, $job->status);
        $this->assertNotNull($job->scheduled_at);
        Queue::assertPushed(ProcessPublishingJob::class);
    }

    public function test_cancel_scheduled_job_records_log(): void
    {
        [$user, $workspace, $item] = $this->fixture();
        $job = PublishingJob::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $item->brand_id,
            'publishing_queue_item_id' => $item->getKey(),
            'created_by' => $user->getKey(),
            'platform' => $item->platform,
            'status' => PublishingJob::STATUS_SCHEDULED,
        ]);

        $this->actingAs($user)->post(route('workspaces.publishing.jobs.cancel', [$workspace, $item, $job]), [
            'comment' => 'Campaign paused.',
        ])->assertRedirect();

        $this->assertSame(PublishingJob::STATUS_CANCELLED, $job->refresh()->status);
        $this->assertDatabaseHas('publishing_logs', [
            'publishing_job_id' => $job->getKey(),
            'event' => 'cancelled',
            'message' => 'Campaign paused.',
        ]);
    }

    public function test_retry_failed_job_dispatches_queue_job(): void
    {
        Queue::fake();

        [$user, $workspace, $item] = $this->fixture();
        $job = PublishingJob::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $item->brand_id,
            'publishing_queue_item_id' => $item->getKey(),
            'created_by' => $user->getKey(),
            'platform' => $item->platform,
            'status' => PublishingJob::STATUS_FAILED,
            'failure_reason' => 'Connector unavailable.',
        ]);

        $this->actingAs($user)->post(route('workspaces.publishing.jobs.retry', [$workspace, $item, $job]))
            ->assertRedirect();

        $this->assertSame(PublishingJob::STATUS_QUEUED, $job->refresh()->status);
        $this->assertNull($job->failure_reason);
        Queue::assertPushed(ProcessPublishingJob::class);
    }

    public function test_processing_job_fails_closed_without_real_connector(): void
    {
        [$user, $workspace, $item, $account] = $this->fixture();
        $job = PublishingJob::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $item->brand_id,
            'publishing_queue_item_id' => $item->getKey(),
            'social_account_id' => $account->getKey(),
            'created_by' => $user->getKey(),
            'platform' => $item->platform,
            'status' => PublishingJob::STATUS_QUEUED,
        ]);

        app(PublishingJobService::class)->process($job->getKey());

        $job->refresh();
        $this->assertSame(PublishingJob::STATUS_FAILED, $job->status);
        $this->assertSame(1, $job->attempts);
        $this->assertStringContainsString('connector is not implemented', $job->failure_reason);
        $this->assertDatabaseHas('publishing_logs', [
            'publishing_job_id' => $job->getKey(),
            'event' => 'failed',
        ]);
    }

    public function test_publishing_queue_view_displays_scheduler_and_logs(): void
    {
        [$user, $workspace, $item] = $this->fixture();
        $job = PublishingJob::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $item->brand_id,
            'publishing_queue_item_id' => $item->getKey(),
            'created_by' => $user->getKey(),
            'platform' => $item->platform,
            'status' => PublishingJob::STATUS_FAILED,
        ]);
        $job->logs()->create([
            'event' => 'failed',
            'level' => 'error',
            'message' => 'No connector.',
        ]);

        $this->actingAs($user)->get(route('workspaces.publishing.show', [$workspace, $item]))
            ->assertOk()
            ->assertSee('Scheduler actions')
            ->assertSee('Publishing jobs')
            ->assertSee('Publishing log')
            ->assertSee('No connector.');
    }

    private function fixture(): array
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->getKey()]);
        WorkspaceUser::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'user_id' => $user->getKey(),
            'role' => WorkspaceUser::ROLE_MARKETING_MANAGER,
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
            'status' => GeneratedContent::STATUS_APPROVED,
        ]);
        $item = PublishingQueueItem::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $brand->getKey(),
            'generated_content_id' => $content->getKey(),
            'created_by' => $user->getKey(),
            'platform' => SocialPlatform::Facebook->value,
            'status' => PublishingQueueItem::STATUS_SCHEDULED,
        ]);
        $account = SocialAccount::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $brand->getKey(),
            'connected_by' => $user->getKey(),
            'platform' => SocialPlatform::Facebook,
        ]);

        return [$user->refresh(), $workspace->refresh(), $item->refresh(), $account->refresh()];
    }
}
