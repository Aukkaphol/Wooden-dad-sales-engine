<?php

namespace Tests\Feature\Director;

use App\Models\AnalyticsRecord;
use App\Models\Asset;
use App\Models\Brand;
use App\Models\GeneratedContent;
use App\Models\PromptTemplate;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceUser;
use App\Services\Director\AiDirectorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class AiDirectorEngineTest extends TestCase
{
    use RefreshDatabase;

    public function test_director_generates_marketing_decisions(): void
    {
        [$user, $workspace, $brand, $content, $prompt, $asset] = $this->fixture();

        $decisions = app(AiDirectorService::class)->decisions($workspace, ['brand_id' => $brand->getKey()]);

        $this->assertArrayHasKey('today', $decisions);
        $this->assertArrayHasKey('best_posting_time', $decisions);
        $this->assertArrayHasKey('suggested_platform', $decisions);
        $this->assertArrayHasKey('suggested_asset', $decisions);
        $this->assertArrayHasKey('suggested_prompt', $decisions);
        $this->assertArrayHasKey('content_quality_score', $decisions);
        $this->assertStringContainsString($prompt->title, $decisions['suggested_prompt']->recommendation);
        $this->assertStringContainsString($asset->name, $decisions['suggested_asset']->recommendation);
        $this->assertSame('facebook', $decisions['suggested_platform']->metadata['platform']);
        $this->assertGreaterThan(0, $decisions['content_quality_score']->metadata['score']);
    }

    public function test_director_page_loads_for_workspace_member(): void
    {
        [$user, $workspace] = $this->fixture();

        $this->actingAs($user)->get(route('workspaces.director.show', $workspace))
            ->assertOk()
            ->assertSee('AI Director')
            ->assertSee("Today's recommendation")
            ->assertSee('Best posting time')
            ->assertSee('Suggested platform');
    }

    public function test_dashboard_displays_director_widgets(): void
    {
        [$user] = $this->fixture();

        $this->actingAs($user)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('AI Director')
            ->assertSee('Content quality score')
            ->assertSee('Suggested prompt')
            ->assertSee('Suggested asset');
    }

    public function test_director_authorization_requires_workspace_access(): void
    {
        [, $workspace] = $this->fixture();
        $outsider = User::factory()->create();
        $outsiderWorkspace = Workspace::factory()->create(['owner_id' => $outsider->getKey()]);
        WorkspaceUser::factory()->create([
            'workspace_id' => $outsiderWorkspace->getKey(),
            'user_id' => $outsider->getKey(),
            'role' => WorkspaceUser::ROLE_OWNER,
        ]);
        $outsider->forceFill(['current_workspace_id' => $outsiderWorkspace->getKey()])->save();

        $this->actingAs($outsider)->get(route('workspaces.director.show', $workspace))
            ->assertForbidden();
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
        $user->forceFill(['current_workspace_id' => $workspace->getKey()])->save();

        $brand = Brand::factory()->create(['workspace_id' => $workspace->getKey()]);
        $prompt = PromptTemplate::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $brand->getKey(),
            'created_by' => $user->getKey(),
            'title' => 'High Converting Prompt',
            'rating_average' => 4.9,
            'success_rate' => 88,
            'usage_count' => 12,
        ]);
        $asset = Asset::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $brand->getKey(),
            'uploaded_by' => $user->getKey(),
            'name' => 'Best Product Photo',
            'status' => Asset::STATUS_READY,
        ]);
        $content = GeneratedContent::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $brand->getKey(),
            'prompt_template_id' => $prompt->getKey(),
            'created_by' => $user->getKey(),
            'title' => 'Winning Content',
            'platform' => 'facebook',
            'content_type' => GeneratedContent::TYPE_FACEBOOK_POST,
            'status' => GeneratedContent::STATUS_PUBLISHED,
            'tags' => ['director'],
        ]);
        DB::table('generated_content_assets')->insert([
            'id' => (string) Str::uuid(),
            'generated_content_id' => $content->getKey(),
            'asset_id' => $asset->getKey(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        AnalyticsRecord::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $brand->getKey(),
            'generated_content_id' => $content->getKey(),
            'platform' => 'facebook',
            'posted_at' => now()->setTime(19, 0),
            'captured_at' => now(),
            'views' => 2500,
            'score' => 86,
            'audience_breakdown' => [
                'gender' => ['female' => 73, 'male' => 26],
                'age' => ['25-34' => 38, '35-44' => 31],
            ],
        ]);

        return [$user->refresh(), $workspace->refresh(), $brand->refresh(), $content->refresh(), $prompt->refresh(), $asset->refresh()];
    }
}
