<?php

namespace Tests\Feature\Prompts;

use App\Jobs\LogActivityJob;
use App\Models\Brand;
use App\Models\PromptTemplate;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\TestCase;

class PromptEngineTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_create_prompt_template_with_version_history(): void
    {
        Queue::fake();

        [$user, $workspace, $brand] = $this->createWorkspaceBrandAndMember(WorkspaceUser::ROLE_MEMBER);

        $response = $this->actingAs($user)->post(route('workspaces.prompts.store', $workspace), [
            'brand_id' => $brand->getKey(),
            'title' => 'Facebook Launch Post',
            'category' => PromptTemplate::CATEGORY_FACEBOOK_POST,
            'platform' => PromptTemplate::PLATFORM_FACEBOOK,
            'prompt_template' => 'Write a {{tone}} post for {{brand_name}} about {{topic}}.',
            'variables' => 'tone, brand_name, topic',
            'example_output' => 'A sample launch post.',
            'status' => PromptTemplate::STATUS_ACTIVE,
            'tags' => 'launch, social',
            'favorite' => '1',
            'recommended_model' => PromptTemplate::MODEL_GPT_55,
        ]);

        $prompt = PromptTemplate::query()->where('title', 'Facebook Launch Post')->firstOrFail();

        $response->assertRedirect(route('workspaces.prompts.show', [$workspace, $prompt]));
        $this->assertTrue(Str::isUuid($prompt->getKey()));
        $this->assertSame($workspace->getKey(), $prompt->workspace_id);
        $this->assertSame($brand->getKey(), $prompt->brand_id);
        $this->assertSame(['tone', 'brand_name', 'topic'], $prompt->variables);
        $this->assertSame(['launch', 'social'], $prompt->tags);
        $this->assertTrue($prompt->favorite);
        $this->assertSame(1, $prompt->versions()->count());
        Queue::assertPushed(LogActivityJob::class);
    }

    public function test_update_creates_new_prompt_version(): void
    {
        Queue::fake();

        [$user, $workspace, $brand] = $this->createWorkspaceBrandAndMember(WorkspaceUser::ROLE_MEMBER);
        $prompt = PromptTemplate::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $brand->getKey(),
            'created_by' => $user->getKey(),
            'version' => 1,
        ]);

        app(\App\Repositories\Contracts\PromptTemplateRepositoryInterface::class)->createVersion($prompt, $user);

        $this->actingAs($user)->put(route('workspaces.prompts.update', [$workspace, $prompt]), [
            'brand_id' => $brand->getKey(),
            'title' => 'Updated SEO Article',
            'category' => PromptTemplate::CATEGORY_SEO_ARTICLE,
            'platform' => PromptTemplate::PLATFORM_WEBSITE,
            'prompt_template' => 'Write an SEO article about {{topic}} for {{brand_name}}.',
            'variables' => 'topic, brand_name',
            'example_output' => 'SEO draft.',
            'status' => PromptTemplate::STATUS_ACTIVE,
            'tags' => 'seo, blog',
            'recommended_model' => PromptTemplate::MODEL_GPT_5_THINKING,
        ])->assertRedirect(route('workspaces.prompts.show', [$workspace, $prompt]));

        $prompt->refresh();

        $this->assertSame(2, $prompt->version);
        $this->assertSame('Updated SEO Article', $prompt->title);
        $this->assertSame(2, $prompt->versions()->count());
        $this->assertDatabaseHas('prompt_template_versions', [
            'prompt_template_id' => $prompt->getKey(),
            'version' => 2,
            'title' => 'Updated SEO Article',
        ]);
    }

    public function test_missing_variable_declaration_is_rejected(): void
    {
        [$user, $workspace, $brand] = $this->createWorkspaceBrandAndMember(WorkspaceUser::ROLE_MEMBER);

        $this->actingAs($user)->post(route('workspaces.prompts.store', $workspace), [
            'brand_id' => $brand->getKey(),
            'title' => 'Bad Prompt',
            'category' => PromptTemplate::CATEGORY_BLOG,
            'platform' => PromptTemplate::PLATFORM_WEBSITE,
            'prompt_template' => 'Write about {{topic}} and {{audience}}.',
            'variables' => 'topic',
            'status' => PromptTemplate::STATUS_DRAFT,
        ])->assertSessionHasErrors('variables');
    }

    public function test_prompt_preview_replaces_variables_and_reports_missing_values(): void
    {
        [$user, $workspace, $brand] = $this->createWorkspaceBrandAndMember(WorkspaceUser::ROLE_MEMBER);
        $prompt = PromptTemplate::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $brand->getKey(),
            'created_by' => $user->getKey(),
            'prompt_template' => 'Write a {{tone}} post about {{topic}}.',
            'variables' => ['tone', 'topic'],
        ]);

        $this->actingAs($user)->post(route('workspaces.prompts.preview', [$workspace, $prompt]), [
            'values' => ['tone' => 'friendly'],
        ])->assertOk()
            ->assertSee('Write a friendly post about')
            ->assertSee('Missing: topic');
    }

    public function test_duplicate_favorite_rating_and_usage_statistics(): void
    {
        Queue::fake();

        [$user, $workspace, $brand] = $this->createWorkspaceBrandAndMember(WorkspaceUser::ROLE_MEMBER);
        $prompt = PromptTemplate::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $brand->getKey(),
            'created_by' => $user->getKey(),
            'favorite' => false,
        ]);

        $this->actingAs($user)->post(route('workspaces.prompts.favorite', [$workspace, $prompt]))
            ->assertSessionHasNoErrors();
        $this->assertTrue($prompt->refresh()->favorite);

        $this->actingAs($user)->post(route('workspaces.prompts.used', [$workspace, $prompt]))
            ->assertSessionHasNoErrors();
        $this->assertSame(1, $prompt->refresh()->usage_count);

        $this->actingAs($user)->post(route('workspaces.prompts.rate', [$workspace, $prompt]), [
            'rating' => 5,
            'successful' => '1',
        ])->assertSessionHasNoErrors();
        $prompt->refresh();
        $this->assertSame(2, $prompt->usage_count);
        $this->assertSame('50.00', $prompt->success_rate);
        $this->assertSame('5.00', $prompt->rating_average);

        $this->actingAs($user)->post(route('workspaces.prompts.duplicate', [$workspace, $prompt]))
            ->assertRedirect();
        $this->assertDatabaseHas('prompt_templates', [
            'title' => $prompt->title.' Copy',
            'version' => 1,
            'favorite' => false,
        ]);
        Queue::assertPushed(LogActivityJob::class);
    }

    public function test_search_and_filter_prompts(): void
    {
        [$user, $workspace, $brand] = $this->createWorkspaceBrandAndMember(WorkspaceUser::ROLE_MEMBER);
        $otherBrand = Brand::factory()->create(['workspace_id' => $workspace->getKey()]);

        PromptTemplate::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $brand->getKey(),
            'created_by' => $user->getKey(),
            'title' => 'TikTok Fleet Script',
            'category' => PromptTemplate::CATEGORY_TIKTOK,
            'platform' => PromptTemplate::PLATFORM_TIKTOK,
            'status' => PromptTemplate::STATUS_ACTIVE,
            'recommended_model' => PromptTemplate::MODEL_GEMINI,
            'tags' => ['fleet'],
            'favorite' => true,
        ]);

        PromptTemplate::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $otherBrand->getKey(),
            'created_by' => $user->getKey(),
            'title' => 'SEO Blog Prompt',
            'category' => PromptTemplate::CATEGORY_BLOG,
            'platform' => PromptTemplate::PLATFORM_WEBSITE,
            'status' => PromptTemplate::STATUS_DRAFT,
            'recommended_model' => PromptTemplate::MODEL_CLAUDE,
            'tags' => ['seo'],
        ]);

        $this->actingAs($user)->get(route('workspaces.prompts.index', [
            $workspace,
            'search' => 'TikTok',
            'brand_id' => $brand->getKey(),
            'category' => PromptTemplate::CATEGORY_TIKTOK,
            'platform' => PromptTemplate::PLATFORM_TIKTOK,
            'status' => PromptTemplate::STATUS_ACTIVE,
            'model' => PromptTemplate::MODEL_GEMINI,
            'favorite' => '1',
            'tag' => 'fleet',
        ]))->assertOk()
            ->assertSee('TikTok Fleet Script')
            ->assertDontSee('SEO Blog Prompt');
    }

    public function test_prompt_is_not_accessible_through_another_workspace_route_and_can_soft_delete(): void
    {
        Queue::fake();

        [$user, $workspace, $brand] = $this->createWorkspaceBrandAndMember(WorkspaceUser::ROLE_MEMBER);
        $otherWorkspace = Workspace::factory()->create();
        $prompt = PromptTemplate::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $brand->getKey(),
            'created_by' => $user->getKey(),
        ]);

        $this->actingAs($user)->get(route('workspaces.prompts.show', [$otherWorkspace, $prompt]))
            ->assertNotFound();

        $this->actingAs($user)->delete(route('workspaces.prompts.destroy', [$workspace, $prompt]))
            ->assertRedirect(route('workspaces.prompts.index', $workspace));

        $this->assertSoftDeleted('prompt_templates', ['id' => $prompt->getKey()]);
    }

    private function createWorkspaceBrandAndMember(string $role): array
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
        $user->forceFill(['current_workspace_id' => $workspace->getKey()])->save();

        return [$user->refresh(), $workspace->refresh(), $brand->refresh()];
    }
}
