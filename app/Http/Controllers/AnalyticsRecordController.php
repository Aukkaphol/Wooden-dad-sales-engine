<?php

namespace App\Http\Controllers;

use App\Http\Requests\Analytics\StoreAnalyticsRecordRequest;
use App\Http\Requests\Analytics\UpdateAnalyticsRecordRequest;
use App\Models\AnalyticsRecord;
use App\Models\Workspace;
use App\Services\Analytics\AnalyticsRecordService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnalyticsRecordController extends Controller
{
    public function index(Request $request, Workspace $workspace, AnalyticsRecordService $service): View
    {
        $this->authorize('viewAny', [AnalyticsRecord::class, $workspace]);

        return view('analytics.index', [
            'workspace' => $workspace->load(['brands', 'generatedContents']),
            'records' => $service->search($workspace, $request->only(['search', 'brand_id', 'platform', 'date_from', 'date_to', 'content_type'])),
            'filters' => $request->only(['search', 'brand_id', 'platform', 'date_from', 'date_to', 'content_type']),
        ]);
    }

    public function create(Workspace $workspace): View
    {
        $this->authorize('create', [AnalyticsRecord::class, $workspace]);

        return view('analytics.create', [
            'workspace' => $workspace->load(['generatedContents.brand', 'publishingQueueItems.generatedContent']),
            'record' => null,
        ]);
    }

    public function store(StoreAnalyticsRecordRequest $request, Workspace $workspace, AnalyticsRecordService $service): RedirectResponse
    {
        $record = $service->create($request->user(), $workspace, $request->validated(), $request);

        return redirect()->route('workspaces.analytics.show', [$workspace, $record])->with('status', 'Analytics record created.');
    }

    public function show(Workspace $workspace, AnalyticsRecord $analytics): View
    {
        $this->ensureRecordBelongsToWorkspace($workspace, $analytics);
        $this->authorize('view', $analytics);

        return view('analytics.show', [
            'workspace' => $workspace,
            'record' => $analytics->load(['brand', 'generatedContent', 'publishingQueueItem', 'creator', 'insights']),
        ]);
    }

    public function edit(Workspace $workspace, AnalyticsRecord $analytics): View
    {
        $this->ensureRecordBelongsToWorkspace($workspace, $analytics);
        $this->authorize('update', $analytics);

        return view('analytics.edit', [
            'workspace' => $workspace->load(['generatedContents.brand', 'publishingQueueItems.generatedContent']),
            'record' => $analytics,
        ]);
    }

    public function update(UpdateAnalyticsRecordRequest $request, Workspace $workspace, AnalyticsRecord $analytics, AnalyticsRecordService $service): RedirectResponse
    {
        $this->ensureRecordBelongsToWorkspace($workspace, $analytics);
        $service->update($request->user(), $analytics, $request->validated(), $request);

        return redirect()->route('workspaces.analytics.show', [$workspace, $analytics])->with('status', 'Analytics record updated.');
    }

    public function destroy(Request $request, Workspace $workspace, AnalyticsRecord $analytics, AnalyticsRecordService $service): RedirectResponse
    {
        $this->ensureRecordBelongsToWorkspace($workspace, $analytics);
        $this->authorize('delete', $analytics);
        $service->delete($request->user(), $analytics, $request);

        return redirect()->route('workspaces.analytics.index', $workspace)->with('status', 'Analytics record deleted.');
    }

    private function ensureRecordBelongsToWorkspace(Workspace $workspace, AnalyticsRecord $record): void
    {
        abort_unless($record->workspace_id === $workspace->getKey(), 404);
    }
}
