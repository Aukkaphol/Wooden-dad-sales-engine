<?php

namespace Tests\Feature\Brands;

use App\Jobs\LogActivityJob;
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

class BrandManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_create_brand_with_logo_and_identity_fields(): void
    {
        Queue::fake();
        Storage::fake('public');

        [$owner, $workspace] = $this->createWorkspaceWithMember(WorkspaceUser::ROLE_OWNER);

        $response = $this->actingAs($owner)->post(route('workspaces.brands.store', $workspace), [
            'name' => 'Wooden Dad Premium',
            'logo' => UploadedFile::fake()->image('logo.png', 300, 300),
            'primary_color' => '#111827',
            'secondary_color' => '#06b6d4',
            'font_family' => 'Inter',
            'tone' => 'premium and practical',
            'voice' => 'Helpful, specific, and confident.',
            'default_prompt' => 'Write premium woodworking content.',
            'default_cta' => 'Request a custom quote',
            'contact_information' => [
                'email' => 'hello@woodendad.test',
                'phone' => '+15555550123',
                'website' => 'https://woodendad.test',
                'address' => '123 Workshop Lane',
            ],
            'social_links' => [
                'facebook' => 'https://facebook.com/woodendad',
                'instagram' => 'https://instagram.com/woodendad',
            ],
        ]);

        $brand = Brand::query()->where('name', 'Wooden Dad Premium')->firstOrFail();

        $response->assertRedirect(route('workspaces.brands.show', [$workspace, $brand]));
        $this->assertTrue(Str::isUuid($brand->getKey()));
        $this->assertSame($workspace->getKey(), $brand->workspace_id);
        $this->assertSame('Request a custom quote', $brand->default_cta);
        $this->assertSame('hello@woodendad.test', $brand->contact_information['email']);
        $this->assertNotNull($brand->logo_path);
        Storage::disk('public')->assertExists($brand->logo_path);
        Queue::assertPushed(LogActivityJob::class);
    }

    public function test_admin_can_update_brand_and_replace_logo(): void
    {
        Queue::fake();
        Storage::fake('public');

        [$admin, $workspace] = $this->createWorkspaceWithMember(WorkspaceUser::ROLE_ADMIN);
        $brand = Brand::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'name' => 'QuickTruck',
            'logo_path' => 'workspaces/old-logo.png',
        ]);
        Storage::disk('public')->put($brand->logo_path, 'old');

        $this->actingAs($admin)->put(route('workspaces.brands.update', [$workspace, $brand]), [
            'name' => 'QuickTruck Fleet',
            'logo' => UploadedFile::fake()->image('fleet.png', 250, 250),
            'primary_color' => '#0f172a',
            'secondary_color' => '#22c55e',
            'font_family' => 'Roboto',
            'tone' => 'direct',
            'voice' => 'Operational and clear.',
            'default_prompt' => 'Write fleet logistics content.',
            'default_cta' => 'Book a fleet consult',
            'contact_information' => [
                'email' => 'fleet@quicktruck.test',
                'website' => 'https://quicktruck.test',
            ],
            'social_links' => [
                'linkedin' => 'https://linkedin.com/company/quicktruck',
            ],
            'status' => 'active',
        ])->assertRedirect(route('workspaces.brands.show', [$workspace, $brand]));

        $brand->refresh();

        $this->assertSame('QuickTruck Fleet', $brand->name);
        $this->assertSame('Roboto', $brand->font_family);
        Storage::disk('public')->assertMissing('workspaces/old-logo.png');
        Storage::disk('public')->assertExists($brand->logo_path);
        Queue::assertPushed(LogActivityJob::class);
    }

    public function test_member_can_view_but_cannot_create_or_update_brand(): void
    {
        [$member, $workspace] = $this->createWorkspaceWithMember(WorkspaceUser::ROLE_MEMBER);
        $brand = Brand::factory()->create(['workspace_id' => $workspace->getKey()]);

        $this->actingAs($member)->get(route('workspaces.brands.show', [$workspace, $brand]))
            ->assertOk()
            ->assertSee($brand->name);

        $this->actingAs($member)->post(route('workspaces.brands.store', $workspace), [
            'name' => 'Unauthorized Brand',
        ])->assertForbidden();

        $this->actingAs($member)->put(route('workspaces.brands.update', [$workspace, $brand]), [
            'name' => 'Unauthorized Edit',
            'status' => 'active',
        ])->assertForbidden();
    }

    public function test_brand_is_not_accessible_through_another_workspace_route(): void
    {
        [$owner, $workspace] = $this->createWorkspaceWithMember(WorkspaceUser::ROLE_OWNER);
        $otherWorkspace = Workspace::factory()->create();
        $brand = Brand::factory()->create(['workspace_id' => $workspace->getKey()]);

        $this->actingAs($owner)->get(route('workspaces.brands.show', [$otherWorkspace, $brand]))
            ->assertNotFound();
    }

    public function test_owner_can_soft_delete_brand(): void
    {
        Queue::fake();

        [$owner, $workspace] = $this->createWorkspaceWithMember(WorkspaceUser::ROLE_OWNER);
        $brand = Brand::factory()->create(['workspace_id' => $workspace->getKey()]);

        $this->actingAs($owner)->delete(route('workspaces.brands.destroy', [$workspace, $brand]))
            ->assertRedirect(route('workspaces.brands.index', $workspace));

        $this->assertSoftDeleted('brands', ['id' => $brand->getKey()]);
        Queue::assertPushed(LogActivityJob::class);
    }

    private function createWorkspaceWithMember(string $role): array
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
