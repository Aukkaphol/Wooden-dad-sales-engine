<?php

namespace App\Http\Controllers;

use App\Http\Requests\Insights\StoreAiInsightRequest;
use App\Http\Requests\Insights\UpdateAiInsightStatusRequest;
use App\Models\AiInsight;
use App\Models\Workspace;
use App\Services\Insights\AiInsightService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AiInsightController extends Controller
{
    public function index(Request $request, Workspace $workspace, AiInsightService $service): View
    {
        $this->authorize('viewAny', [AiInsight::class, $workspace]);

        return view('insights.index', [
            'workspace' => $workspace->load(['brands', 'generatedContents']),
            'insights' => $service->search($workspace, $request->only(['search', 'brand_id', 'content_id', 'insight_type', 'status', 'priority'])),
            'filters' => $request->only(['search', 'brand_id', 'content_id', 'insight_type', 'status', 'priority']),
        ]);
    }

    public function create(Workspace $workspace): View
    {
        $this->authorize('create', [AiInsight::class, $workspace]);

        return view('insights.create', [
            'workspace' => $workspace->load(['generatedContents.brand', 'analyticsRecords.generatedContent']),
        ]);
    }

    public function store(StoreAiInsightRequest $request, Workspace $workspace, AiInsightService $service): RedirectResponse
    {
        $insight = $service->create($request->user(), $workspace, $request->validated(), $request);

        return redirect()->route('workspaces.insights.show', [$workspace, $insight])->with('status', 'AI insight created.');
    }

    public function show(Workspace $workspace, AiInsight $insight): View
    {
        $this->ensureInsightBelongsToWorkspace($workspace, $insight);
        $this->authorize('view', $insight);

        return view('insights.show', [
            'workspace' => $workspace,
            'insight' => $insight->load(['brand', 'generatedContent', 'analyticsRecord', 'creator']),
        ]);
    }

    public function updateStatus(UpdateAiInsightStatusRequest $request, Workspace $workspace, AiInsight $insight, AiInsightService $service): RedirectResponse
    {
        $this->ensureInsightBelongsToWorkspace($workspace, $insight);
        $service->updateStatus($request->user(), $insight, $request->validated('status'), $request);

        return back()->with('status', 'AI insight status updated.');
    }

    public function destroy(Request $request, Workspace $workspace, AiInsight $insight, AiInsightService $service): RedirectResponse
    {
        $this->ensureInsightBelongsToWorkspace($workspace, $insight);
        $this->authorize('delete', $insight);
        $service->delete($request->user(), $insight, $request);

        return redirect()->route('workspaces.insights.index', $workspace)->with('status', 'AI insight deleted.');
    }

    private function ensureInsightBelongsToWorkspace(Workspace $workspace, AiInsight $insight): void
    {
        abort_unless($insight->workspace_id === $workspace->getKey(), 404);
    }
}
