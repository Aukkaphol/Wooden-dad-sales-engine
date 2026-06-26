<?php

namespace Tests\Feature\Release;

use App\Jobs\ProcessPublishingJob;
use App\Models\AiInsight;
use App\Models\AnalyticsRecord;
use App\Models\Asset;
use App\Models\Brand;
use App\Models\GeneratedContent;
use App\Models\MediaPipelineRun;
use App\Models\PromptTemplate;
use App\Models\PublishingQueueItem;
use App\Models\SocialAccount;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ProductionReadinessTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_seed_data_connects_v1_workflow_modules(): void
    {
        $this->seed();

        $director = User::query()->where('email', 'director@example.com')->firstOrFail();

        $this->assertNotNull($director->current_workspace_id);
        $this->assertGreaterThanOrEqual(2, Workspace::query()->count());
        $this->assertGreaterThanOrEqual(4, Brand::query()->count());
        $this->assertGreaterThanOrEqual(4, Asset::query()->count());
        $this->assertGreaterThanOrEqual(4, PromptTemplate::query()->count());
        $this->assertGreaterThanOrEqual(4, GeneratedContent::query()->count());
        $this->assertGreaterThanOrEqual(4, PublishingQueueItem::query()->count());
        $this->assertGreaterThanOrEqual(4, AnalyticsRecord::query()->count());
        $this->assertGreaterThanOrEqual(4, AiInsight::query()->count());
        $this->assertGreaterThanOrEqual(4, SocialAccount::query()->count());
        $this->assertGreaterThanOrEqual(4, MediaPipelineRun::query()->count());

        $content = GeneratedContent::query()->firstOrFail();

        $this->assertDatabaseHas('generated_content_assets', [
            'generated_content_id' => $content->getKey(),
        ]);
        $this->assertTrue(DB::table('analytics_records')->where('generated_content_id', $content->getKey())->exists());
        $this->assertTrue(DB::table('ai_insights')->where('generated_content_id', $content->getKey())->exists());
    }

    public function test_release_configuration_keeps_external_integrations_disabled_and_queue_ready(): void
    {
        $this->assertFalse(config('jarvis.features.openai_enabled'));
        $this->assertFalse(config('jarvis.features.external_publishing_enabled'));
        $this->assertFalse(config('jarvis.features.video_editing_enabled'));

        $job = new ProcessPublishingJob('publishing-job-id');

        $this->assertSame(config('jarvis.queue.publishing'), $job->queue);
    }
}
