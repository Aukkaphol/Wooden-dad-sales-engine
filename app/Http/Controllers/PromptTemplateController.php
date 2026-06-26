<?php

namespace App\Http\Controllers;

use App\Http\Requests\Prompts\PreviewPromptTemplateRequest;
use App\Http\Requests\Prompts\RatePromptTemplateRequest;
use App\Http\Requests\Prompts\StorePromptTemplateRequest;
use App\Http\Requests\Prompts\UpdatePromptTemplateRequest;
use App\Models\PromptTemplate;
use App\Models\Workspace;
use App\Services\Prompts\PromptTemplateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PromptTemplateController extends Controller
{
    public function index(Request $request, Workspace $workspace, PromptTemplateService $service): View
    {
        $this->authorize('view', $workspace);

        return view('prompts.index', [
            'workspace' => $workspace->load('brands'),
            'prompts' => $service->search($workspace, $request->only(['search', 'brand_id', 'category', 'platform', 'status', 'model', 'favorite', 'tag'])),
            'filters' => $request->only(['search', 'brand_id', 'category', 'platform', 'status', 'model', 'favorite', 'tag']),
        ]);
    }

    public function create(Workspace $workspace): View
    {
        $this->authorize('create', [PromptTemplate::class, $workspace]);

        return view('prompts.create', [
            'workspace' => $workspace->load('brands'),
        ]);
    }

    public function store(StorePromptTemplateRequest $request, Workspace $workspace, PromptTemplateService $service): RedirectResponse
    {
        $prompt = $service->create($request->user(), $workspace, $request->brand(), $request->validated(), $request);

        return redirect()->route('workspaces.prompts.show', [$workspace, $prompt])->with('status', 'Prompt created.');
    }

    public function show(Workspace $workspace, PromptTemplate $prompt): View
    {
        $this->ensurePromptBelongsToWorkspace($workspace, $prompt);
        $this->authorize('view', $prompt);

        return view('prompts.show', [
            'workspace' => $workspace,
            'prompt' => $prompt->load(['brand', 'creator', 'versions.creator']),
            'preview' => null,
            'missingVariables' => [],
        ]);
    }

    public function edit(Workspace $workspace, PromptTemplate $prompt): View
    {
        $this->ensurePromptBelongsToWorkspace($workspace, $prompt);
        $this->authorize('update', $prompt);

        return view('prompts.edit', [
            'workspace' => $workspace->load('brands'),
            'prompt' => $prompt,
        ]);
    }

    public function update(UpdatePromptTemplateRequest $request, Workspace $workspace, PromptTemplate $prompt, PromptTemplateService $service): RedirectResponse
    {
        $this->ensurePromptBelongsToWorkspace($workspace, $prompt);
        $service->update($request->user(), $prompt, $request->brand(), $request->validated(), $request);

        return redirect()->route('workspaces.prompts.show', [$workspace, $prompt])->with('status', 'Prompt updated.');
    }

    public function destroy(Request $request, Workspace $workspace, PromptTemplate $prompt, PromptTemplateService $service): RedirectResponse
    {
        $this->ensurePromptBelongsToWorkspace($workspace, $prompt);
        $this->authorize('delete', $prompt);

        $service->delete($request->user(), $prompt, $request);

        return redirect()->route('workspaces.prompts.index', $workspace)->with('status', 'Prompt deleted.');
    }

    public function duplicate(Request $request, Workspace $workspace, PromptTemplate $prompt, PromptTemplateService $service): RedirectResponse
    {
        $this->ensurePromptBelongsToWorkspace($workspace, $prompt);
        $this->authorize('duplicate', $prompt);

        $duplicate = $service->duplicate($request->user(), $prompt, $request);

        return redirect()->route('workspaces.prompts.edit', [$workspace, $duplicate])->with('status', 'Prompt duplicated.');
    }

    public function favorite(Request $request, Workspace $workspace, PromptTemplate $prompt, PromptTemplateService $service): RedirectResponse
    {
        $this->ensurePromptBelongsToWorkspace($workspace, $prompt);
        $this->authorize('update', $prompt);

        $service->toggleFavorite($request->user(), $prompt, $request);

        return back()->with('status', 'Favorite updated.');
    }

    public function preview(PreviewPromptTemplateRequest $request, Workspace $workspace, PromptTemplate $prompt, PromptTemplateService $service): View
    {
        $this->ensurePromptBelongsToWorkspace($workspace, $prompt);
        $result = $service->preview($prompt, $request->validated('values', []));

        return view('prompts.show', [
            'workspace' => $workspace,
            'prompt' => $prompt->load(['brand', 'creator', 'versions.creator']),
            'preview' => $result['preview'],
            'missingVariables' => $result['missing'],
        ]);
    }

    public function rate(RatePromptTemplateRequest $request, Workspace $workspace, PromptTemplate $prompt, PromptTemplateService $service): RedirectResponse
    {
        $this->ensurePromptBelongsToWorkspace($workspace, $prompt);
        $service->rate($request->user(), $prompt, (int) $request->integer('rating'), $request->boolean('successful'), $request);

        return back()->with('status', 'Prompt rating saved.');
    }

    public function markUsed(Request $request, Workspace $workspace, PromptTemplate $prompt, PromptTemplateService $service): RedirectResponse
    {
        $this->ensurePromptBelongsToWorkspace($workspace, $prompt);
        $this->authorize('view', $prompt);

        $service->markUsed($request->user(), $prompt, $request);

        return back()->with('status', 'Usage recorded.');
    }

    private function ensurePromptBelongsToWorkspace(Workspace $workspace, PromptTemplate $prompt): void
    {
        abort_unless($prompt->workspace_id === $workspace->getKey(), 404);
    }
}
