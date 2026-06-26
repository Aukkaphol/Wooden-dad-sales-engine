<?php

namespace Database\Seeders;

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
use App\Models\WorkspaceUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $director = User::factory()->create([
            'name' => 'Jarvis Director',
            'email' => 'director@example.com',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);

        $woodenDad = $this->workspace($director, 'Wooden Dad', 'wooden-dad');
        $quickTruck = $this->workspace($director, 'QuickTruck', 'quicktruck');

        $this->demoBrand($director, $woodenDad, 'Wooden Dad Design', 'facebook');
        $this->demoBrand($director, $woodenDad, 'Wooden Dad Premium', 'instagram');
        $this->demoBrand($director, $quickTruck, 'QuickTruck Fleet', 'facebook');
        $this->demoBrand($director, $quickTruck, 'QuickTruck Parts', 'tiktok');

        $director->forceFill(['current_workspace_id' => $quickTruck->getKey()])->save();
    }

    private function workspace(User $owner, string $name, string $slug): Workspace
    {
        $workspace = Workspace::factory()->create([
            'owner_id' => $owner->getKey(),
            'name' => $name,
            'slug' => $slug,
            'industry' => 'Marketing',
            'timezone' => 'Asia/Bangkok',
            'default_language' => 'en',
            'status' => 'active',
        ]);

        WorkspaceUser::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'user_id' => $owner->getKey(),
            'role' => WorkspaceUser::ROLE_OWNER,
            'joined_at' => now(),
        ]);

        return $workspace;
    }

    private function demoBrand(User $user, Workspace $workspace, string $name, string $platform): void
    {
        $brand = Brand::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'name' => $name,
            'primary_color' => '#22d3ee',
            'tone' => 'confident',
            'voice' => 'clear and helpful',
            'default_cta' => 'Message us today',
        ]);

        $asset = Asset::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $brand->getKey(),
            'uploaded_by' => $user->getKey(),
            'name' => $name.' Hero Image',
            'status' => Asset::STATUS_READY,
            'category' => 'campaign',
            'tags' => ['demo', 'hero'],
        ]);

        $prompt = PromptTemplate::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $brand->getKey(),
            'created_by' => $user->getKey(),
            'title' => $name.' Launch Prompt',
            'platform' => $platform,
            'prompt_template' => 'Create a high-converting '.$platform.' post for {{ product }} with a strong hook and CTA.',
            'variables' => ['product' => 'Demo offer'],
            'status' => PromptTemplate::STATUS_ACTIVE,
            'favorite' => true,
            'usage_count' => 8,
            'success_rate' => 82,
            'rating_average' => 4.7,
        ]);

        $content = GeneratedContent::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $brand->getKey(),
            'prompt_template_id' => $prompt->getKey(),
            'created_by' => $user->getKey(),
            'title' => $name.' V1 Demo Campaign',
            'platform' => $platform,
            'content_type' => GeneratedContent::TYPE_FACEBOOK_POST,
            'prompt_snapshot' => $prompt->prompt_template,
            'variables' => ['product' => 'Demo offer'],
            'status' => GeneratedContent::STATUS_PUBLISHED,
            'tags' => ['demo', 'v1'],
            'notes' => 'Seeded demo content for production review.',
        ]);

        DB::table('generated_content_assets')->insert([
            'id' => (string) Str::uuid(),
            'generated_content_id' => $content->getKey(),
            'asset_id' => $asset->getKey(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $queue = PublishingQueueItem::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $brand->getKey(),
            'generated_content_id' => $content->getKey(),
            'created_by' => $user->getKey(),
            'platform' => $platform,
            'status' => PublishingQueueItem::STATUS_PUBLISHED,
            'published_at' => now()->subDay(),
        ]);

        $analytics = AnalyticsRecord::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $brand->getKey(),
            'generated_content_id' => $content->getKey(),
            'publishing_queue_item_id' => $queue->getKey(),
            'created_by' => $user->getKey(),
            'platform' => $platform,
            'views' => 2400,
            'reach' => 1800,
            'impressions' => 3100,
            'likes' => 140,
            'comments' => 24,
            'shares' => 18,
            'saves' => 11,
            'follows_gained' => 7,
            'link_clicks' => 36,
            'score' => 84,
            'audience_breakdown' => [
                'gender' => ['female' => 61, 'male' => 37, 'unknown' => 2],
                'age' => ['25-34' => 36, '35-44' => 28, '45-54' => 18],
            ],
        ]);

        AiInsight::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $brand->getKey(),
            'generated_content_id' => $content->getKey(),
            'analytics_record_id' => $analytics->getKey(),
            'created_by' => $user->getKey(),
            'title' => $name.' demo insight',
            'recommendation' => 'Reuse the strongest hook and test a short-form variation this week.',
        ]);

        SocialAccount::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $brand->getKey(),
            'connected_by' => $user->getKey(),
            'platform' => $platform,
            'name' => $name.' Social Account',
            'status' => SocialAccount::STATUS_DRAFT,
        ]);

        MediaPipelineRun::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $brand->getKey(),
            'created_by' => $user->getKey(),
            'asset_ids' => [$asset->getKey()],
            'prompt_template_id' => $prompt->getKey(),
            'prompt_version' => $prompt->version,
            'generated_content_id' => $content->getKey(),
            'publishing_queue_item_id' => $queue->getKey(),
            'analytics_record_id' => $analytics->getKey(),
            'current_stage' => MediaPipelineRun::STAGE_INSIGHTS,
            'status' => MediaPipelineRun::STATUS_INSIGHT_CREATED,
        ]);
    }
}
