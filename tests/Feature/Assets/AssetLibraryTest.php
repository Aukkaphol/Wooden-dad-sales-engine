<?php

namespace Tests\Feature\Assets;

use App\Jobs\LogActivityJob;
use App\Models\Asset;
use App\Models\Brand;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class AssetLibraryTest extends TestCase
{
    use RefreshDatabase;

    public function test_workspace_member_can_upload_image_asset_with_metadata_and_tags(): void
    {
        Queue::fake();
        Storage::fake('local');

        [$user, $workspace, $brand] = $this->createWorkspaceBrandAndMember(WorkspaceUser::ROLE_MEMBER);

        $response = $this->actingAs($user)->post(route('workspaces.assets.store', $workspace), [
            'brand_id' => $brand->getKey(),
            'name' => 'Launch Image',
            'file' => UploadedFile::fake()->image('launch.png', 1200, 800),
            'category' => 'social',
            'tags' => 'launch, Instagram, launch',
            'status' => Asset::STATUS_READY,
        ]);

        $asset = Asset::query()->where('name', 'Launch Image')->firstOrFail();

        $response->assertRedirect(route('workspaces.assets.show', [$workspace, $asset]));
        $this->assertTrue(Str::isUuid($asset->getKey()));
        $this->assertSame(Asset::TYPE_IMAGE, $asset->type);
        $this->assertSame($workspace->getKey(), $asset->workspace_id);
        $this->assertSame($brand->getKey(), $asset->brand_id);
        $this->assertSame($user->getKey(), $asset->uploaded_by);
        $this->assertSame(['launch', 'instagram'], $asset->tags);
        $this->assertSame(1200, $asset->width);
        $this->assertSame(800, $asset->height);
        $this->assertStringContainsString("workspaces/{$workspace->getKey()}/brands/{$brand->getKey()}/assets/{$asset->getKey()}", $asset->path);
        Storage::disk('local')->assertExists($asset->path);
        Queue::assertPushed(LogActivityJob::class);
    }

    public function test_file_type_detection_supports_video_audio_document_template_and_logo(): void
    {
        Queue::fake();
        Storage::fake('local');

        [$user, $workspace, $brand] = $this->createWorkspaceBrandAndMember(WorkspaceUser::ROLE_OWNER);

        $files = [
            ['Video Clip', UploadedFile::fake()->create('clip.mp4', 2048, 'video/mp4'), Asset::TYPE_VIDEO],
            ['Audio Track', UploadedFile::fake()->create('track.mp3', 1024, 'audio/mpeg'), Asset::TYPE_AUDIO],
            ['Spec Document', UploadedFile::fake()->create('brief.pdf', 512, 'application/pdf'), Asset::TYPE_DOCUMENT],
            ['Template Pack', UploadedFile::fake()->create('templates.zip', 512, 'application/zip'), Asset::TYPE_TEMPLATE],
            ['Brand Logo', UploadedFile::fake()->image('logo.png', 400, 400), Asset::TYPE_LOGO],
        ];

        foreach ($files as [$name, $file, $expectedType]) {
            $this->actingAs($user)->post(route('workspaces.assets.store', $workspace), [
                'brand_id' => $brand->getKey(),
                'name' => $name,
                'file' => $file,
                'status' => Asset::STATUS_DRAFT,
            ])->assertSessionHasNoErrors();

            $this->assertDatabaseHas('assets', [
                'name' => $name,
                'type' => $expectedType,
            ]);
        }
    }

    public function test_search_and_filter_assets_by_type_status_brand_category_and_tag(): void
    {
        [$user, $workspace, $brand] = $this->createWorkspaceBrandAndMember(WorkspaceUser::ROLE_MEMBER);
        $otherBrand = Brand::factory()->create(['workspace_id' => $workspace->getKey()]);

        Asset::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $brand->getKey(),
            'uploaded_by' => $user->getKey(),
            'name' => 'Fleet Launch Video',
            'type' => Asset::TYPE_VIDEO,
            'status' => Asset::STATUS_READY,
            'category' => 'campaign',
            'tags' => ['fleet', 'launch'],
        ]);

        Asset::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $otherBrand->getKey(),
            'uploaded_by' => $user->getKey(),
            'name' => 'Parts Document',
            'type' => Asset::TYPE_DOCUMENT,
            'status' => Asset::STATUS_DRAFT,
            'category' => 'internal',
            'tags' => ['parts'],
        ]);

        $this->actingAs($user)->get(route('workspaces.assets.index', [
            $workspace,
            'search' => 'Fleet',
            'brand_id' => $brand->getKey(),
            'type' => Asset::TYPE_VIDEO,
            'status' => Asset::STATUS_READY,
            'category' => 'campaign',
            'tag' => 'fleet',
        ]))->assertOk()
            ->assertSee('Fleet Launch Video')
            ->assertDontSee('Parts Document');
    }

    public function test_asset_cannot_use_brand_from_another_workspace(): void
    {
        Storage::fake('local');

        [$user, $workspace] = $this->createWorkspaceAndMember(WorkspaceUser::ROLE_OWNER);
        $externalBrand = Brand::factory()->create();

        $this->actingAs($user)->post(route('workspaces.assets.store', $workspace), [
            'brand_id' => $externalBrand->getKey(),
            'name' => 'Wrong Brand',
            'file' => UploadedFile::fake()->image('wrong.png'),
            'status' => Asset::STATUS_READY,
        ])->assertSessionHasErrors('brand_id');
    }

    public function test_uploader_can_update_replace_file_and_soft_delete_asset(): void
    {
        Queue::fake();
        Storage::fake('local');

        [$user, $workspace, $brand] = $this->createWorkspaceBrandAndMember(WorkspaceUser::ROLE_MEMBER);

        $this->actingAs($user)->post(route('workspaces.assets.store', $workspace), [
            'brand_id' => $brand->getKey(),
            'name' => 'Original Asset',
            'file' => UploadedFile::fake()->image('original.png', 100, 100),
            'status' => Asset::STATUS_DRAFT,
        ]);

        $asset = Asset::query()->where('name', 'Original Asset')->firstOrFail();
        $oldPath = $asset->path;

        $this->actingAs($user)->put(route('workspaces.assets.update', [$workspace, $asset]), [
            'brand_id' => $brand->getKey(),
            'name' => 'Updated Asset',
            'file' => UploadedFile::fake()->image('updated.jpg', 200, 150),
            'category' => 'ads',
            'tags' => 'paid, creative',
            'status' => Asset::STATUS_READY,
        ])->assertRedirect(route('workspaces.assets.show', [$workspace, $asset]));

        $asset->refresh();

        Storage::disk('local')->assertMissing($oldPath);
        Storage::disk('local')->assertExists($asset->path);
        $this->assertSame('Updated Asset', $asset->name);
        $this->assertSame(['paid', 'creative'], $asset->tags);
        $this->assertSame(Asset::STATUS_READY, $asset->status);

        $this->actingAs($user)->delete(route('workspaces.assets.destroy', [$workspace, $asset]))
            ->assertRedirect(route('workspaces.assets.index', $workspace));

        $this->assertSoftDeleted('assets', ['id' => $asset->getKey()]);
        Queue::assertPushed(LogActivityJob::class);
    }

    public function test_other_member_cannot_update_asset_uploaded_by_someone_else(): void
    {
        [$uploader, $workspace, $brand] = $this->createWorkspaceBrandAndMember(WorkspaceUser::ROLE_MEMBER);
        $otherMember = User::factory()->create();
        WorkspaceUser::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'user_id' => $otherMember->getKey(),
            'role' => WorkspaceUser::ROLE_MEMBER,
        ]);

        $asset = Asset::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $brand->getKey(),
            'uploaded_by' => $uploader->getKey(),
        ]);

        $this->actingAs($otherMember)->put(route('workspaces.assets.update', [$workspace, $asset]), [
            'brand_id' => $brand->getKey(),
            'name' => 'Not Allowed',
            'status' => Asset::STATUS_READY,
        ])->assertForbidden();
    }

    private function createWorkspaceBrandAndMember(string $role): array
    {
        [$user, $workspace] = $this->createWorkspaceAndMember($role);
        $brand = Brand::factory()->create(['workspace_id' => $workspace->getKey()]);

        return [$user, $workspace, $brand];
    }

    private function createWorkspaceAndMember(string $role): array
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

        $user->forceFill(['current_workspace_id' => $workspace->getKey()])->save();

        return [$user->refresh(), $workspace->refresh()];
    }
}
