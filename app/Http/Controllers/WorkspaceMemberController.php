<?php

namespace App\Http\Controllers;

use App\Http\Requests\Workspaces\StoreWorkspaceMemberRequest;
use App\Http\Requests\Workspaces\UpdateWorkspaceMemberRequest;
use App\Models\User;
use App\Models\Workspace;
use App\Services\Workspaces\WorkspaceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WorkspaceMemberController extends Controller
{
    public function store(StoreWorkspaceMemberRequest $request, Workspace $workspace, WorkspaceService $workspaceService): RedirectResponse
    {
        $workspaceService->addMember(
            actor: $request->user(),
            workspace: $workspace,
            email: $request->string('email')->toString(),
            role: $request->string('role')->toString(),
            request: $request,
        );

        return redirect()->route('workspaces.show', $workspace)->with('status', 'Member added.');
    }

    public function update(
        UpdateWorkspaceMemberRequest $request,
        Workspace $workspace,
        User $user,
        WorkspaceService $workspaceService,
    ): RedirectResponse {
        $workspaceService->updateMemberRole($request->user(), $workspace, $user, $request->string('role')->toString(), $request);

        return redirect()->route('workspaces.show', $workspace)->with('status', 'Member role updated.');
    }

    public function destroy(Request $request, Workspace $workspace, User $user, WorkspaceService $workspaceService): RedirectResponse
    {
        $this->authorize('manageMembers', $workspace);

        $workspaceService->removeMember($request->user(), $workspace, $user, $request);

        return redirect()->route('workspaces.show', $workspace)->with('status', 'Member removed.');
    }
}
