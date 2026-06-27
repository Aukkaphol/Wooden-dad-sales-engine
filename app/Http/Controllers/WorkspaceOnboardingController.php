<?php

namespace App\Http\Controllers;

use App\Http\Requests\Workspaces\StoreWorkspaceRequest;
use App\Services\Workspaces\WorkspaceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkspaceOnboardingController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if ($request->user()->hasAnyWorkspace()) {
            return redirect()->route('dashboard');
        }

        return view('onboarding.workspace');
    }

    public function store(StoreWorkspaceRequest $request, WorkspaceService $workspaceService): RedirectResponse
    {
        if ($request->user()->hasAnyWorkspace()) {
            return redirect()->route('dashboard');
        }

        $workspace = $workspaceService->create($request->user(), $request->validated(), $request);

        return redirect()->route('dashboard')->with('status', $workspace->name.' is ready.');
    }
}
