<?php

namespace App\Http\Controllers;

use App\Http\Requests\Contents\PreviewGeneratedContentRequest;
use App\Http\Requests\Contents\StoreGeneratedContentRequest;
use App\Http\Requests\Contents\UpdateGeneratedContentRequest;
use App\Models\GeneratedContent;
use App\Models\Workspace;
use App\Services\Contents\GeneratedContentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GeneratedContentController extends Controller
{
    public function index(Request $request, Workspace $workspace, GeneratedContentService $service): View
    {
        $this->authorize('view', $workspace);

        return view('contents.index', [
            'workspace' => $workspace->load(['brands', 'promptTemplates']),
            'contents' => $service->search($workspace, $request->only(['search', 'brand_id', 'prompt_template_id', 'platform', 'content_type', 'status', 'tag'])),
            'filters' => $request->only(['search', 'brand_id', 'prompt_template_id', 'platform', 'content_type', 'status', 'tag']),
        ]);
    }

    public function create(Workspace $workspace): View
    {
        $this->authorize('create', [GeneratedContent::class, $workspace]);

        return view('contents.create', [
            'workspace' => $workspace->load(['brands', 'promptTemplates', 'assets']),
        ]);
    }

    public function store(StoreGeneratedContentRequest $request, Workspace $workspace, GeneratedContentService $service): RedirectResponse
    {
        $content = $service->create($request->user(), $workspace, $request->validated(), $request);

        return redirect()->route('workspaces.contents.show', [$workspace, $content])->with('status', 'Content draft generated.');
    }

    public function show(Workspace $workspace, GeneratedContent $content): View
    {
        $this->ensureContentBelongsToWorkspace($workspace, $content);
        $this->authorize('view', $content);

        return view('contents.show', [
            'workspace' => $workspace,
            'content' => $content->load(['brand', 'promptTemplate', 'assets', 'creator', 'versions.creator', 'approvalHistories.reviewer']),
            'preview' => null,
        ]);
    }

    public function edit(Workspace $workspace, GeneratedContent $content): View
    {
        $this->ensureContentBelongsToWorkspace($workspace, $content);
        $this->authorize('update', $content);

        return view('contents.edit', [
            'workspace' => $workspace->load(['brands', 'promptTemplates', 'assets']),
            'content' => $content,
        ]);
    }

    public function update(UpdateGeneratedContentRequest $request, Workspace $workspace, GeneratedContent $content, GeneratedContentService $service): RedirectResponse
    {
        $this->ensureContentBelongsToWorkspace($workspace, $content);
        $service->update($request->user(), $content, $request->validated(), $request);

        return redirect()->route('workspaces.contents.show', [$workspace, $content])->with('status', 'Content updated.');
    }

    public function destroy(Request $request, Workspace $workspace, GeneratedContent $content, GeneratedContentService $service): RedirectResponse
    {
        $this->ensureContentBelongsToWorkspace($workspace, $content);
        $this->authorize('delete', $content);

        $service->delete($request->user(), $content, $request);

        return redirect()->route('workspaces.contents.index', $workspace)->with('status', 'Content deleted.');
    }

    public function duplicate(Request $request, Workspace $workspace, GeneratedContent $content, GeneratedContentService $service): RedirectResponse
    {
        $this->ensureContentBelongsToWorkspace($workspace, $content);
        $this->authorize('duplicate', $content);

        $duplicate = $service->duplicate($request->user(), $content, $request);

        return redirect()->route('workspaces.contents.edit', [$workspace, $duplicate])->with('status', 'Content duplicated.');
    }

    public function preview(PreviewGeneratedContentRequest $request, Workspace $workspace, GeneratedContent $content, GeneratedContentService $service): View
    {
        $this->ensureContentBelongsToWorkspace($workspace, $content);

        return view('contents.show', [
            'workspace' => $workspace,
            'content' => $content->load(['brand', 'promptTemplate', 'assets', 'creator', 'versions.creator', 'approvalHistories.reviewer']),
            'preview' => $service->preview($content, $request->validated('variables', [])),
        ]);
    }

    private function ensureContentBelongsToWorkspace(Workspace $workspace, GeneratedContent $content): void
    {
        abort_unless($content->workspace_id === $workspace->getKey(), 404);
    }
}
