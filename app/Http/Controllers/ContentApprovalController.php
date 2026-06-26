<?php

namespace App\Http\Controllers;

use App\Http\Requests\Approvals\ContentApprovalDecisionRequest;
use App\Http\Requests\Approvals\SchedulePublishingRequest;
use App\Models\GeneratedContent;
use App\Models\Workspace;
use App\Services\Approvals\ContentApprovalService;
use Illuminate\Http\RedirectResponse;

class ContentApprovalController extends Controller
{
    public function submit(ContentApprovalDecisionRequest $request, Workspace $workspace, GeneratedContent $content, ContentApprovalService $service): RedirectResponse
    {
        $this->ensureContentBelongsToWorkspace($workspace, $content);
        $this->authorize('submitForReview', $content);
        $service->submitForReview($request->user(), $content, $request->validated(), $request);

        return back()->with('status', 'Content submitted for review.');
    }

    public function approve(ContentApprovalDecisionRequest $request, Workspace $workspace, GeneratedContent $content, ContentApprovalService $service): RedirectResponse
    {
        $this->ensureContentBelongsToWorkspace($workspace, $content);
        $this->authorize('approve', $content);
        $service->approve($request->user(), $content, $request->validated(), $request);

        return back()->with('status', 'Content approved.');
    }

    public function reject(ContentApprovalDecisionRequest $request, Workspace $workspace, GeneratedContent $content, ContentApprovalService $service): RedirectResponse
    {
        $this->ensureContentBelongsToWorkspace($workspace, $content);
        $this->authorize('reject', $content);
        $service->reject($request->user(), $content, $request->validated(), $request);

        return back()->with('status', 'Content rejected.');
    }

    public function return(ContentApprovalDecisionRequest $request, Workspace $workspace, GeneratedContent $content, ContentApprovalService $service): RedirectResponse
    {
        $this->ensureContentBelongsToWorkspace($workspace, $content);
        $this->authorize('returnWithComment', $content);
        $service->returnWithComment($request->user(), $content, $request->validated(), $request);

        return back()->with('status', 'Content returned with comment.');
    }

    public function schedule(SchedulePublishingRequest $request, Workspace $workspace, GeneratedContent $content, ContentApprovalService $service): RedirectResponse
    {
        $this->ensureContentBelongsToWorkspace($workspace, $content);
        $this->authorize('schedule', $content);
        $service->schedule($request->user(), $content, $request->validated(), $request);

        return back()->with('status', 'Content scheduled.');
    }

    public function publish(ContentApprovalDecisionRequest $request, Workspace $workspace, GeneratedContent $content, ContentApprovalService $service): RedirectResponse
    {
        $this->ensureContentBelongsToWorkspace($workspace, $content);
        $this->authorize('publish', $content);
        $service->markPublished($request->user(), $content, $request->validated(), $request);

        return back()->with('status', 'Content marked published.');
    }

    public function archive(ContentApprovalDecisionRequest $request, Workspace $workspace, GeneratedContent $content, ContentApprovalService $service): RedirectResponse
    {
        $this->ensureContentBelongsToWorkspace($workspace, $content);
        $this->authorize('archive', $content);
        $service->archive($request->user(), $content, $request->validated(), $request);

        return back()->with('status', 'Content archived.');
    }

    private function ensureContentBelongsToWorkspace(Workspace $workspace, GeneratedContent $content): void
    {
        abort_unless($content->workspace_id === $workspace->getKey(), 404);
    }
}
