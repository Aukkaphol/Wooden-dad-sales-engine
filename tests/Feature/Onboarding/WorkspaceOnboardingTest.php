<?php

namespace Tests\Feature\Onboarding;

use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class WorkspaceOnboardingTest extends TestCase
{
    use RefreshDatabase;

    public function test_registered_user_without_workspace_sees_onboarding(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('onboarding.workspace.create'));

        $this->actingAs($user)
            ->get(route('onboarding.workspace.create'))
            ->assertOk()
            ->assertSee('Create your first workspace');
    }

    public function test_onboarding_creates_first_workspace_and_assigns_owner(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('onboarding.workspace.store'), [
                'name' => 'Wooden Dad',
                'industry' => 'Woodworking',
                'timezone' => 'Asia/Bangkok',
                'default_language' => 'en',
            ])
            ->assertRedirect(route('dashboard'));

        $workspace = Workspace::query()->where('name', 'Wooden Dad')->firstOrFail();

        $this->assertTrue(Str::isUuid($workspace->getKey()));
        $this->assertSame($user->getKey(), $workspace->owner_id);
        $this->assertSame($workspace->getKey(), $user->refresh()->current_workspace_id);
        $this->assertDatabaseHas('workspace_users', [
            'workspace_id' => $workspace->getKey(),
            'user_id' => $user->getKey(),
            'role' => WorkspaceUser::ROLE_OWNER,
            'deleted_at' => null,
        ]);
    }

    public function test_user_with_workspace_is_redirected_away_from_onboarding(): void
    {
        [$user] = $this->workspaceOwnerFixture();

        $this->actingAs($user)
            ->get(route('onboarding.workspace.create'))
            ->assertRedirect(route('dashboard'));
    }

    public function test_super_admin_without_workspace_can_access_admin(): void
    {
        $superAdmin = User::factory()->create(['system_role' => 'super_admin']);
        Workspace::factory()->create(['name' => 'QuickTruck']);

        $this->actingAs($superAdmin)
            ->get(route('admin.workspaces.index'))
            ->assertOk()
            ->assertSee('QuickTruck');

        $this->actingAs($superAdmin)
            ->get(route('dashboard'))
            ->assertRedirect(route('admin.workspaces.index'));
    }

    public function test_non_owner_without_workspace_cannot_access_admin_and_gets_onboarding_for_studio(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.workspaces.index'))
            ->assertRedirect(route('onboarding.workspace.create'));

        $this->actingAs($user)
            ->get(route('channels.facebook.index'))
            ->assertRedirect(route('onboarding.workspace.create'));
    }

    private function workspaceOwnerFixture(): array
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->getKey()]);
        WorkspaceUser::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'user_id' => $user->getKey(),
            'role' => WorkspaceUser::ROLE_OWNER,
        ]);
        $user->forceFill(['current_workspace_id' => $workspace->getKey()])->save();

        return [$user->refresh(), $workspace->refresh()];
    }
}
