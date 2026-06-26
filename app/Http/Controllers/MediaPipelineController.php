<?php

namespace App\Http\Controllers;

use App\Http\Requests\Pipeline\MediaPipelineActionRequest;
use App\Http\Requests\Pipeline\QueueMediaPipelineRequest;
use App\Http\Requests\Pipeline\StoreMediaPipelineRequest;
use App\Http\Requests\Pipeline\UpdatePipelineAnalyticsRequest;
use App\Models\MediaPipelineRun;
use App\Models\Workspace;
use App\Services\Pipeline\MediaPipelineService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MediaPipelineController extends Controller
{
    public function index(Request $request, Workspace $workspace, MediaPipelineService $service): View
    {
        $this->authorize('viewAny', [MediaPipelineRun::class, $workspace]);

        return view('pipeline.index', [
            'workspace' => $workspace->load(['brands', 'assets.brand', 'promptTemplates.brand']),
            'pipelines' => $service->search($workspace, $request->only(['brand_id', 'status', 'stage'])),
            'filters' => $request->only(['brand_id', 'status', 'stage']),
        ]);
    }

    public function store(StoreMediaPipelineRequest $request, Workspace $workspace, MediaPipelineService $service): RedirectResponse
    {
        $pipeline = $service->start($request->user(), $workspace, $request->validated(), $request);

        return redirect()->route('workspaces.pipeline.show', [$workspace, $pipeline])->with('status', 'Media pipeline started and submitted for approval.');
    }

    public function show(Workspace $workspace, MediaPipelineRun $pipeline): View
    {
        $this->ensurePipelineBelongsToWorkspace($workspace, $pipeline);
        $this->authorize('view', $pipeline);

        return view('pipeline.show', [
            'workspace' => $workspace,
            'pipeline' => $pipeline->load([
                'brand',
                'promptTemplate',
                'generatedContent.assets',
                'publishingQueueItem',
                'analyticsRecord',
                'aiInsight',
                'histories.actor',
            ]),
        ]);
    }

    public function approve(MediaPipelineActionRequest $request, Workspace $workspace, MediaPipelineRun $pipeline, MediaPipelineService $service): RedirectResponse
    {
        $this->ensurePipelineBelongsToWorkspace($workspace, $pipeline);
        $service->approve($request->user(), $pipeline, $request->validated(), $request);

        return back()->with('status', 'Pipeline content approved.');
    }

    public function reject(MediaPipelineActionRequest $request, Workspace $workspace, MediaPipelineRun $pipeline, MediaPipelineService $service): RedirectResponse
    {
        $this->ensurePipelineBelongsToWorkspace($workspace, $pipeline);
        $service->reject($request->user(), $pipeline, $request->validated(), $request);

        return back()->with('status', 'Pipeline content rejected.');
    }

    public function revision(MediaPipelineActionRequest $request, Workspace $workspace, MediaPipelineRun $pipeline, MediaPipelineService $service): RedirectResponse
    {
        $this->ensurePipelineBelongsToWorkspace($workspace, $pipeline);
        $service->requestRevision($request->user(), $pipeline, $request->validated(), $request);

        return back()->with('status', 'Pipeline content returned for revision.');
    }

    public function queue(QueueMediaPipelineRequest $request, Workspace $workspace, MediaPipelineRun $pipeline, MediaPipelineService $service): RedirectResponse
    {
        $this->ensurePipelineBelongsToWorkspace($workspace, $pipeline);
        $service->queue($request->user(), $workspace, $pipeline, $request->validated(), $request);

        return back()->with('status', 'Pipeline content queued.');
    }

    public function publish(MediaPipelineActionRequest $request, Workspace $workspace, MediaPipelineRun $pipeline, MediaPipelineService $service): RedirectResponse
    {
        $this->ensurePipelineBelongsToWorkspace($workspace, $pipeline);
        $service->publish($request->user(), $pipeline, $request->validated(), $request);

        return back()->with('status', 'Pipeline content marked published and analytics record created.');
    }

    public function cancel(MediaPipelineActionRequest $request, Workspace $workspace, MediaPipelineRun $pipeline, MediaPipelineService $service): RedirectResponse
    {
        $this->ensurePipelineBelongsToWorkspace($workspace, $pipeline);
        $service->cancel($request->user(), $pipeline, $request->validated(), $request);

        return back()->with('status', 'Pipeline queue item cancelled.');
    }

    public function analytics(UpdatePipelineAnalyticsRequest $request, Workspace $workspace, MediaPipelineRun $pipeline, MediaPipelineService $service): RedirectResponse
    {
        $this->ensurePipelineBelongsToWorkspace($workspace, $pipeline);
        $service->updateAnalytics($request->user(), $pipeline, $request->validated(), $request);

        return back()->with('status', 'Pipeline analytics updated and insight refreshed.');
    }

    private function ensurePipelineBelongsToWorkspace(Workspace $workspace, MediaPipelineRun $pipeline): void
    {
        abort_unless($pipeline->workspace_id === $workspace->getKey(), 404);
    }
}
