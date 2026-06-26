<?php

namespace App\Http\Controllers;

use App\Http\Requests\Workspaces\StoreWorkspaceRequest;
use App\Http\Requests\Workspaces\SwitchWorkspaceRequest;
use App\Http\Requests\Workspaces\UpdateWorkspaceRequest;
use App\Models\Workspace;
use App\Services\Workspaces\WorkspaceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkspaceController extends Controller
{
    public function index(Request $request, WorkspaceService $workspaceService): View
    {
        return view('workspaces.index', [
            'workspaces' => $workspaceService->listForUser($request->user()),
        ]);
    }

    public function create(): View
    {
        return view('workspaces.create');
    }

    public function store(StoreWorkspaceRequest $request, WorkspaceService $workspaceService): RedirectResponse
    {
        $workspace = $workspaceService->create($request->user(), $request->validated(), $request);

        return redirect()->route('workspaces.show', $workspace)->with('status', 'Workspace created.');
    }

    public function show(Workspace $workspace): View
    {
        $this->authorize('view', $workspace);

        return view('workspaces.show', [
            'workspace' => $workspace->load(['owner', 'memberships.user']),
        ]);
    }

    public function edit(Workspace $workspace): View
    {
        $this->authorize('update', $workspace);

        return view('workspaces.edit', [
            'workspace' => $workspace,
        ]);
    }

    public function update(UpdateWorkspaceRequest $request, Workspace $workspace, WorkspaceService $workspaceService): RedirectResponse
    {
        $workspaceService->update($request->user(), $workspace, $request->validated(), $request);

        return redirect()->route('workspaces.show', $workspace)->with('status', 'Workspace updated.');
    }

    public function destroy(Request $request, Workspace $workspace, WorkspaceService $workspaceService): RedirectResponse
    {
        $this->authorize('delete', $workspace);

        $workspaceService->delete($request->user(), $workspace, $request);

        return redirect()->route('workspaces.index')->with('status', 'Workspace deleted.');
    }

    public function switch(SwitchWorkspaceRequest $request, Workspace $workspace, WorkspaceService $workspaceService): RedirectResponse
    {
        $workspaceService->switch($request->user(), $workspace, $request);

        return back()->with('status', 'Workspace switched.');
    }
}
