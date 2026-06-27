<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminWorkspaceController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAdmin($request->user());

        return view('admin.workspaces.index', [
            'workspaces' => Workspace::query()
                ->with('owner')
                ->withCount(['memberships as members_count' => fn ($query) => $query->whereNull('deleted_at')])
                ->latest()
                ->paginate(20),
        ]);
    }

    public function members(Request $request, Workspace $workspace): View
    {
        $this->authorizeAdmin($request->user());

        $workspace->load([
            'owner',
            'memberships' => fn ($query) => $query->with('user')->whereNull('deleted_at')->latest('joined_at'),
        ]);

        return view('admin.workspaces.members', [
            'workspace' => $workspace,
            'roles' => WorkspaceUser::ADMIN_ASSIGNABLE_ROLES,
        ]);
    }

    public function storeMember(Request $request, Workspace $workspace): RedirectResponse
    {
        $this->authorizeAdmin($request->user());

        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'role' => ['required', Rule::in(WorkspaceUser::ADMIN_ASSIGNABLE_ROLES)],
        ], [
            'email.exists' => 'No user exists with that email address.',
        ]);

        $user = User::query()->where('email', $validated['email'])->firstOrFail();

        DB::transaction(function () use ($request, $workspace, $user, $validated): void {
            $membership = WorkspaceUser::withTrashed()->firstOrNew([
                'workspace_id' => $workspace->getKey(),
                'user_id' => $user->getKey(),
            ]);

            $membership->fill([
                'role' => $validated['role'],
                'invited_by' => $request->user()->getKey(),
                'joined_at' => $membership->joined_at ?? now(),
            ]);

            if ($membership->trashed()) {
                $membership->restore();
            }

            $membership->save();

            if ($validated['role'] === WorkspaceUser::ROLE_OWNER && $workspace->owner_id !== $user->getKey()) {
                $workspace->forceFill(['owner_id' => $user->getKey()])->save();
            }
        });

        return back()->with('status', 'Workspace member saved.');
    }

    public function updateMember(Request $request, Workspace $workspace, User $user): RedirectResponse
    {
        $this->authorizeAdmin($request->user());

        $validated = $request->validate([
            'role' => ['required', Rule::in(WorkspaceUser::ADMIN_ASSIGNABLE_ROLES)],
        ]);

        $membership = $this->activeMembership($workspace, $user);

        if ($membership->role === WorkspaceUser::ROLE_OWNER && $validated['role'] !== WorkspaceUser::ROLE_OWNER && $this->activeOwnerCount($workspace) <= 1) {
            return back()->withErrors(['role' => 'A workspace must have at least one owner.']);
        }

        DB::transaction(function () use ($workspace, $user, $membership, $validated): void {
            $membership->forceFill(['role' => $validated['role']])->save();

            if ($validated['role'] === WorkspaceUser::ROLE_OWNER && $workspace->owner_id !== $user->getKey()) {
                $workspace->forceFill(['owner_id' => $user->getKey()])->save();
            }
        });

        return back()->with('status', 'Workspace role updated.');
    }

    public function destroyMember(Request $request, Workspace $workspace, User $user): RedirectResponse
    {
        $this->authorizeAdmin($request->user());

        $membership = $this->activeMembership($workspace, $user);

        if ($membership->role === WorkspaceUser::ROLE_OWNER && $this->activeOwnerCount($workspace) <= 1) {
            return back()->withErrors(['member' => 'Cannot remove the last owner from a workspace.']);
        }

        $membership->delete();

        return back()->with('status', 'Workspace member removed.');
    }

    public function usersWithoutWorkspace(Request $request): View
    {
        $this->authorizeAdmin($request->user());

        return view('admin.users.no-workspace', [
            'users' => User::query()
                ->whereDoesntHave('workspaceMemberships', fn ($query) => $query->whereNull('deleted_at'))
                ->orderBy('name')
                ->paginate(20),
            'workspaces' => Workspace::query()->orderBy('name')->get(),
            'roles' => WorkspaceUser::ADMIN_ASSIGNABLE_ROLES,
        ]);
    }

    public function assignUser(Request $request, User $user): RedirectResponse
    {
        $this->authorizeAdmin($request->user());

        $validated = $request->validate([
            'workspace_id' => ['required', 'exists:workspaces,id'],
            'role' => ['required', Rule::in(WorkspaceUser::ADMIN_ASSIGNABLE_ROLES)],
        ]);

        $workspace = Workspace::query()->findOrFail($validated['workspace_id']);

        WorkspaceUser::query()->create([
            'workspace_id' => $workspace->getKey(),
            'user_id' => $user->getKey(),
            'role' => $validated['role'],
            'invited_by' => $request->user()->getKey(),
            'joined_at' => now(),
        ]);

        if ($validated['role'] === WorkspaceUser::ROLE_OWNER && $workspace->owner_id !== $user->getKey()) {
            $workspace->forceFill(['owner_id' => $user->getKey()])->save();
        }

        return back()->with('status', 'User assigned to workspace.');
    }

    private function authorizeAdmin(User $user): void
    {
        abort_unless($user->ownedWorkspaces()->exists() || $user->hasWorkspaceRole(
            Workspace::query()->whereKey($user->current_workspace_id)->first() ?? new Workspace(),
            WorkspaceUser::ROLE_OWNER,
        ), 403);
    }

    private function activeMembership(Workspace $workspace, User $user): WorkspaceUser
    {
        return WorkspaceUser::query()
            ->where('workspace_id', $workspace->getKey())
            ->where('user_id', $user->getKey())
            ->whereNull('deleted_at')
            ->firstOrFail();
    }

    private function activeOwnerCount(Workspace $workspace): int
    {
        return WorkspaceUser::query()
            ->where('workspace_id', $workspace->getKey())
            ->where('role', WorkspaceUser::ROLE_OWNER)
            ->whereNull('deleted_at')
            ->count();
    }
}
