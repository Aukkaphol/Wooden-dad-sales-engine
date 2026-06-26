<?php

namespace Tests\Feature\Contents;

use App\Jobs\LogActivityJob;
use App\Models\Asset;
use App\Models\Brand;
use App\Models\GeneratedContent;
use App\Models\PromptTemplate;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\TestCase;

class ContentGeneratorTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_create_provider_ready_generated_content_with_assets_and_version(): void
    {
        Queue::fake();

        [$user, $workspace, $brand, $prompt, $asset] = $this->fixture();

        $response = $this->actingAs($user)->post(route('workspaces.contents.store', $workspace), [
            'brand_id' => $brand->getKey(),
            'prompt_template_id' => $prompt->getKey(),
            'asset_ids' => [$asset->getKey()],
            'title' => 'Launch Draft',
            'platform' => 'facebook',
            'content_type' => GeneratedContent::TYPE_FACEBOOK_POST,
            'variables' => ['topic' => 'new product'],
            'status' => GeneratedContent::STATUS_DRAFT,
            'tags' => 'launch, social',
            'notes' => 'First provider-ready draft.',
        ]);

        $content = GeneratedContent::query()->where('title', 'Launch Draft')->firstOrFail();

        $response->assertRedirect(route('workspaces.contents.show', [$workspace, $content]));
        $this->assertTrue(Str::isUuid($content->getKey()));
        $this->assertSame($workspace->getKey(), $content->workspace_id);
        $this->assertSame($brand->getKey(), $content->brand_id);
        $this->assertSame($prompt->prompt_template, $content->prompt_snapshot);
        $this->assertStringContainsString('[DRAFT CONTENT - AI PROVIDER NOT CONNECTED]', $content->generated_content);
        $this->assertSame(['launch', 'social'], $content->tags);
        $this->assertSame(1, $content->assets()->count());
        $this->assertSame(1, $content->versions()->count());
        Queue::assertPushed(LogActivityJob::class);
    }

    public function test_update_creates_new_content_version(): void
    {
        Queue::fake();

        [$user, $workspace, $brand, $prompt, $asset] = $this->fixture();
        $content = GeneratedContent::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $brand->getKey(),
            'prompt_template_id' => $prompt->getKey(),
            'created_by' => $user->getKey(),
            'version' => 1,
        ]);
        app(\App\Repositories\Contracts\GeneratedContentRepositoryInterface::class)->createVersion($content, $user);

        $this->actingAs($user)->put(route('workspaces.contents.update', [$workspace, $content]), [
            'brand_id' => $brand->getKey(),
            'prompt_template_id' => $prompt->getKey(),
            'asset_ids' => [$asset->getKey()],
            'title' => 'Updated Draft',
            'platform' => 'instagram',
            'content_type' => GeneratedContent::TYPE_INSTAGRAM_CAPTION,
            'generated_content' => 'Updated copy.',
            'variables' => ['topic' => 'updated'],
            'status' => GeneratedContent::STATUS_DRAFT,
            'tags' => 'approved',
            'notes' => 'Reviewed.',
        ])->assertRedirect(route('workspaces.contents.show', [$workspace, $content]));

        $content->refresh();
        $this->assertSame(2, $content->version);
        $this->assertSame(GeneratedContent::STATUS_DRAFT, $content->status);
        $this->assertSame(2, $content->versions()->count());
        $this->assertSame(1, $content->assets()->count());
    }

    public function test_preview_and_duplicate_workflows(): void
    {
        Queue::fake();

        [$user, $workspace, $brand, $prompt] = $this->fixtureWithoutAsset();
        $content = GeneratedContent::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $brand->getKey(),
            'prompt_template_id' => $prompt->getKey(),
            'created_by' => $user->getKey(),
            'prompt_snapshot' => 'Write about {{topic}}.',
            'variables' => ['topic' => 'old topic'],
            'generated_content' => 'Mock result.',
        ]);

        $this->actingAs($user)->post(route('workspaces.contents.preview', [$workspace, $content]), [
            'variables' => ['topic' => 'new topic'],
        ])->assertOk()
            ->assertSee('Write about new topic.')
            ->assertSee('Mock result.');

        $this->actingAs($user)->post(route('workspaces.contents.duplicate', [$workspace, $content]))
            ->assertRedirect();

        $this->assertDatabaseHas('generated_contents', [
            'title' => $content->title.' Copy',
            'version' => 1,
            'status' => GeneratedContent::STATUS_DRAFT,
        ]);
        Queue::assertPushed(LogActivityJob::class);
    }

    public function test_search_and_filter_generated_contents(): void
    {
        [$user, $workspace, $brand, $prompt] = $this->fixtureWithoutAsset();
        $otherBrand = Brand::factory()->create(['workspace_id' => $workspace->getKey()]);

        GeneratedContent::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $brand->getKey(),
            'prompt_template_id' => $prompt->getKey(),
            'created_by' => $user->getKey(),
            'title' => 'Fleet Facebook Draft',
            'platform' => 'facebook',
            'content_type' => GeneratedContent::TYPE_FACEBOOK_POST,
            'status' => GeneratedContent::STATUS_DRAFT,
            'tags' => ['fleet'],
        ]);

        GeneratedContent::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $otherBrand->getKey(),
            'prompt_template_id' => $prompt->getKey(),
            'created_by' => $user->getKey(),
            'title' => 'Blog Draft',
            'platform' => 'website',
            'content_type' => GeneratedContent::TYPE_BLOG,
            'status' => GeneratedContent::STATUS_APPROVED,
            'tags' => ['blog'],
        ]);

        $this->actingAs($user)->get(route('workspaces.contents.index', [
            $workspace,
            'search' => 'Fleet',
            'brand_id' => $brand->getKey(),
            'prompt_template_id' => $prompt->getKey(),
            'platform' => 'facebook',
            'content_type' => GeneratedContent::TYPE_FACEBOOK_POST,
            'status' => GeneratedContent::STATUS_DRAFT,
            'tag' => 'fleet',
        ]))->assertOk()
            ->assertSee('Fleet Facebook Draft')
            ->assertDontSee('Blog Draft');
    }

    public function test_content_is_workspace_scoped_and_soft_deletable(): void
    {
        Queue::fake();

        [$user, $workspace, $brand, $prompt] = $this->fixtureWithoutAsset();
        $otherWorkspace = Workspace::factory()->create();
        $content = GeneratedContent::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $brand->getKey(),
            'prompt_template_id' => $prompt->getKey(),
            'created_by' => $user->getKey(),
        ]);

        $this->actingAs($user)->get(route('workspaces.contents.show', [$otherWorkspace, $content]))
            ->assertNotFound();

        $this->actingAs($user)->delete(route('workspaces.contents.destroy', [$workspace, $content]))
            ->assertRedirect(route('workspaces.contents.index', $workspace));

        $this->assertSoftDeleted('generated_contents', ['id' => $content->getKey()]);
    }

    public function test_other_member_cannot_update_content_created_by_someone_else(): void
    {
        [$creator, $workspace, $brand, $prompt] = $this->fixtureWithoutAsset();
        $other = User::factory()->create();
        WorkspaceUser::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'user_id' => $other->getKey(),
            'role' => WorkspaceUser::ROLE_MEMBER,
        ]);

        $content = GeneratedContent::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $brand->getKey(),
            'prompt_template_id' => $prompt->getKey(),
            'created_by' => $creator->getKey(),
        ]);

        $this->actingAs($other)->put(route('workspaces.contents.update', [$workspace, $content]), [
            'brand_id' => $brand->getKey(),
            'prompt_template_id' => $prompt->getKey(),
            'title' => 'Nope',
            'platform' => 'facebook',
            'content_type' => GeneratedContent::TYPE_FACEBOOK_POST,
            'generated_content' => 'Nope',
            'status' => GeneratedContent::STATUS_DRAFT,
        ])->assertForbidden();
    }

    private function fixture(): array
    {
        [$user, $workspace, $brand, $prompt] = $this->fixtureWithoutAsset();
        $asset = Asset::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $brand->getKey(),
            'uploaded_by' => $user->getKey(),
        ]);

        return [$user, $workspace, $brand, $prompt, $asset];
    }

    private function fixtureWithoutAsset(): array
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => User::factory()]);
        WorkspaceUser::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'user_id' => $user->getKey(),
            'role' => WorkspaceUser::ROLE_MEMBER,
        ]);
        $brand = Brand::factory()->create(['workspace_id' => $workspace->getKey()]);
        $prompt = PromptTemplate::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $brand->getKey(),
            'created_by' => $user->getKey(),
            'prompt_template' => 'Write a content draft about {{topic}}.',
            'variables' => ['topic'],
        ]);

        $user->forceFill(['current_workspace_id' => $workspace->getKey()])->save();

        return [$user->refresh(), $workspace->refresh(), $brand->refresh(), $prompt->refresh()];
    }
}
