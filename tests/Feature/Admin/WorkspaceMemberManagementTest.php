<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkspaceMemberManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_workspaces(): void
    {
        [$admin, $workspace] = $this->adminFixture();
        $other = Workspace::factory()->create(['name' => 'QuickTruck']);
        WorkspaceUser::factory()->create([
            'workspace_id' => $other->getKey(),
            'user_id' => User::factory()->create()->getKey(),
            'role' => WorkspaceUser::ROLE_OWNER,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.workspaces.index'))
            ->assertOk()
            ->assertSee($workspace->name)
            ->assertSee('QuickTruck')
            ->assertSee('View / Manage');
    }

    public function test_super_admin_can_manage_all_workspaces_without_membership(): void
    {
        $superAdmin = User::factory()->create(['system_role' => 'super_admin']);
        $workspace = Workspace::factory()->create(['name' => 'Future Company']);

        $this->actingAs($superAdmin)
            ->get(route('admin.workspaces.members', $workspace))
            ->assertOk()
            ->assertSee('Future Company');
    }

    public function test_admin_can_add_user_to_workspace(): void
    {
        [$admin, $workspace] = $this->adminFixture();
        $user = User::factory()->create(['email' => 'editor@example.com']);

        $this->actingAs($admin)
            ->post(route('admin.workspaces.members.store', $workspace), [
                'email' => 'editor@example.com',
                'role' => WorkspaceUser::ROLE_EDITOR,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('workspace_users', [
            'workspace_id' => $workspace->getKey(),
            'user_id' => $user->getKey(),
            'role' => WorkspaceUser::ROLE_EDITOR,
            'deleted_at' => null,
        ]);
    }

    public function test_admin_can_change_role(): void
    {
        [$admin, $workspace] = $this->adminFixture();
        $user = User::factory()->create();
        WorkspaceUser::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'user_id' => $user->getKey(),
            'role' => WorkspaceUser::ROLE_VIEWER,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.workspaces.members.update', [$workspace, $user]), [
                'role' => WorkspaceUser::ROLE_ADMIN,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('workspace_users', [
            'workspace_id' => $workspace->getKey(),
            'user_id' => $user->getKey(),
            'role' => WorkspaceUser::ROLE_ADMIN,
            'deleted_at' => null,
        ]);
    }

    public function test_admin_can_remove_member(): void
    {
        [$admin, $workspace] = $this->adminFixture();
        $user = User::factory()->create();
        WorkspaceUser::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'user_id' => $user->getKey(),
            'role' => WorkspaceUser::ROLE_VIEWER,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.workspaces.members.destroy', [$workspace, $user]))
            ->assertRedirect();

        $this->assertSoftDeleted('workspace_users', [
            'workspace_id' => $workspace->getKey(),
            'user_id' => $user->getKey(),
        ]);
    }

    public function test_cannot_remove_last_owner(): void
    {
        [$admin, $workspace] = $this->adminFixture();

        $this->actingAs($admin)
            ->delete(route('admin.workspaces.members.destroy', [$workspace, $admin]))
            ->assertRedirect()
            ->assertSessionHasErrors('member');

        $this->assertDatabaseHas('workspace_users', [
            'workspace_id' => $workspace->getKey(),
            'user_id' => $admin->getKey(),
            'role' => WorkspaceUser::ROLE_OWNER,
            'deleted_at' => null,
        ]);
    }

    public function test_cannot_change_last_owner_role(): void
    {
        [$admin, $workspace] = $this->adminFixture();

        $this->actingAs($admin)
            ->put(route('admin.workspaces.members.update', [$workspace, $admin]), [
                'role' => WorkspaceUser::ROLE_ADMIN,
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('role');

        $this->assertDatabaseHas('workspace_users', [
            'workspace_id' => $workspace->getKey(),
            'user_id' => $admin->getKey(),
            'role' => WorkspaceUser::ROLE_OWNER,
            'deleted_at' => null,
        ]);
    }

    public function test_no_workspace_users_appear_and_can_be_assigned(): void
    {
        [$admin, $workspace] = $this->adminFixture();
        $orphan = User::factory()->create(['name' => 'No Workspace User']);
        $member = User::factory()->create(['name' => 'Existing Member']);
        WorkspaceUser::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'user_id' => $member->getKey(),
            'role' => WorkspaceUser::ROLE_VIEWER,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.users.no-workspace'))
            ->assertOk()
            ->assertSee('No Workspace User')
            ->assertDontSee('Existing Member');

        $this->actingAs($admin)
            ->post(route('admin.users.assign-workspace', $orphan), [
                'workspace_id' => $workspace->getKey(),
                'role' => WorkspaceUser::ROLE_VIEWER,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('workspace_users', [
            'workspace_id' => $workspace->getKey(),
            'user_id' => $orphan->getKey(),
            'role' => WorkspaceUser::ROLE_VIEWER,
            'deleted_at' => null,
        ]);
    }

    private function adminFixture(): array
    {
        $admin = User::factory()->create();
        $workspace = Workspace::factory()->create([
            'owner_id' => $admin->getKey(),
            'name' => 'Wooden Dad',
        ]);
        WorkspaceUser::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'user_id' => $admin->getKey(),
            'role' => WorkspaceUser::ROLE_OWNER,
            'joined_at' => now(),
        ]);
        $admin->forceFill(['current_workspace_id' => $workspace->getKey()])->save();

        return [$admin->refresh(), $workspace->refresh()];
    }
}
