<?php

namespace Tests\Feature\Workspaces;

use App\Jobs\LogActivityJob;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\TestCase;

class WorkspaceManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_workspace_as_owner_and_current_workspace(): void
    {
        Queue::fake();

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/workspaces', [
            'name' => 'Wooden Dad Design',
            'industry' => 'Woodworking',
            'timezone' => 'Asia/Bangkok',
            'default_language' => 'en',
        ]);

        $workspace = Workspace::query()->where('name', 'Wooden Dad Design')->firstOrFail();

        $response->assertRedirect(route('workspaces.show', $workspace));
        $this->assertTrue(Str::isUuid($workspace->getKey()));
        $this->assertSame($workspace->getKey(), $user->refresh()->current_workspace_id);
        $this->assertDatabaseHas('workspace_users', [
            'workspace_id' => $workspace->getKey(),
            'user_id' => $user->getKey(),
            'role' => WorkspaceUser::ROLE_OWNER,
        ]);
        Queue::assertPushed(LogActivityJob::class);
    }

    public function test_owner_can_update_and_soft_delete_workspace(): void
    {
        Queue::fake();

        [$owner, $workspace] = $this->createOwnedWorkspace();

        $this->actingAs($owner)->put(route('workspaces.update', $workspace), [
            'name' => 'QuickTruck',
            'industry' => 'Logistics',
            'timezone' => 'UTC',
            'default_language' => 'en',
            'status' => 'active',
        ])->assertRedirect(route('workspaces.show', $workspace));

        $this->assertDatabaseHas('workspaces', [
            'id' => $workspace->getKey(),
            'name' => 'QuickTruck',
            'industry' => 'Logistics',
        ]);

        $this->actingAs($owner)->delete(route('workspaces.destroy', $workspace))
            ->assertRedirect(route('workspaces.index'));

        $this->assertSoftDeleted('workspaces', ['id' => $workspace->getKey()]);
        Queue::assertPushed(LogActivityJob::class);
    }

    public function test_member_cannot_update_workspace(): void
    {
        [$owner, $workspace] = $this->createOwnedWorkspace();
        $member = User::factory()->create();
        WorkspaceUser::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'user_id' => $member->getKey(),
            'role' => WorkspaceUser::ROLE_MEMBER,
        ]);

        $this->actingAs($member)->put(route('workspaces.update', $workspace), [
            'name' => 'Unauthorized Edit',
            'timezone' => 'UTC',
            'default_language' => 'en',
            'status' => 'active',
        ])->assertForbidden();

        $this->assertSame($owner->getKey(), $workspace->refresh()->owner_id);
    }

    public function test_admin_can_manage_members_but_cannot_remove_owner(): void
    {
        Queue::fake();

        [$owner, $workspace] = $this->createOwnedWorkspace();
        $admin = User::factory()->create(['email' => 'admin@example.test']);
        $member = User::factory()->create(['email' => 'member@example.test']);

        WorkspaceUser::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'user_id' => $admin->getKey(),
            'role' => WorkspaceUser::ROLE_ADMIN,
        ]);

        $this->actingAs($admin)->post(route('workspaces.members.store', $workspace), [
            'email' => 'member@example.test',
            'role' => WorkspaceUser::ROLE_MEMBER,
        ])->assertRedirect(route('workspaces.show', $workspace));

        $this->assertDatabaseHas('workspace_users', [
            'workspace_id' => $workspace->getKey(),
            'user_id' => $member->getKey(),
            'role' => WorkspaceUser::ROLE_MEMBER,
        ]);

        $this->actingAs($admin)->delete(route('workspaces.members.destroy', [$workspace, $owner]))
            ->assertSessionHasErrors('member');
    }

    public function test_user_can_switch_between_accessible_workspaces_only(): void
    {
        Queue::fake();

        [$owner, $firstWorkspace] = $this->createOwnedWorkspace();
        $secondWorkspace = Workspace::factory()->create(['owner_id' => $owner->getKey()]);
        WorkspaceUser::factory()->create([
            'workspace_id' => $secondWorkspace->getKey(),
            'user_id' => $owner->getKey(),
            'role' => WorkspaceUser::ROLE_OWNER,
        ]);

        $outsiderWorkspace = Workspace::factory()->create();

        $this->actingAs($owner)->post(route('workspaces.switch', $secondWorkspace))
            ->assertSessionHasNoErrors();

        $this->assertSame($secondWorkspace->getKey(), $owner->refresh()->current_workspace_id);

        $this->actingAs($owner)->post(route('workspaces.switch', $outsiderWorkspace))
            ->assertForbidden();

        $this->assertSame($firstWorkspace->owner_id, $owner->getKey());
    }

    private function createOwnedWorkspace(): array
    {
        $owner = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $owner->getKey()]);

        WorkspaceUser::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'user_id' => $owner->getKey(),
            'role' => WorkspaceUser::ROLE_OWNER,
        ]);

        $owner->forceFill(['current_workspace_id' => $workspace->getKey()])->save();

        return [$owner->refresh(), $workspace->refresh()];
    }
}
