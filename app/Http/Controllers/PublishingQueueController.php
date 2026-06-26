<?php

namespace App\Http\Controllers;

use App\Http\Requests\Publishing\PublishingQueueActionRequest;
use App\Http\Requests\Publishing\SchedulePublishingJobRequest;
use App\Http\Requests\Publishing\StorePublishingQueueItemRequest;
use App\Models\PublishingJob;
use App\Models\PublishingQueueItem;
use App\Models\SocialAccount;
use App\Models\Workspace;
use App\Services\Publishing\PublishingSchedulerService;
use App\Services\Publishing\PublishingQueueService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PublishingQueueController extends Controller
{
    public function index(Request $request, Workspace $workspace, PublishingQueueService $service): View
    {
        $this->authorize('view', $workspace);

        return view('publishing.index', [
            'workspace' => $workspace->load(['brands', 'generatedContents']),
            'items' => $service->search($workspace, $request->only(['search', 'brand_id', 'content_id', 'platform', 'status'])),
            'filters' => $request->only(['search', 'brand_id', 'content_id', 'platform', 'status']),
        ]);
    }

    public function create(Workspace $workspace): View
    {
        $this->authorize('create', [PublishingQueueItem::class, $workspace]);

        return view('publishing.create', [
            'workspace' => $workspace->load(['generatedContents.brand']),
        ]);
    }

    public function store(StorePublishingQueueItemRequest $request, Workspace $workspace, PublishingQueueService $service): RedirectResponse
    {
        $item = $service->schedule($request->user(), $workspace, $request->validated(), $request);

        return redirect()->route('workspaces.publishing.show', [$workspace, $item])->with('status', 'Publishing queue item scheduled.');
    }

    public function show(Workspace $workspace, PublishingQueueItem $publishing): View
    {
        $this->ensureItemBelongsToWorkspace($workspace, $publishing);
        $this->authorize('view', $publishing);

        return view('publishing.show', [
            'workspace' => $workspace,
            'item' => $publishing->load(['brand', 'generatedContent', 'creator', 'histories.actor', 'publishingJobs.logs', 'publishingJobs.socialAccount']),
            'socialAccounts' => SocialAccount::query()
                ->where('workspace_id', $workspace->getKey())
                ->where(fn ($query) => $query->whereNull('brand_id')->orWhere('brand_id', $publishing->brand_id))
                ->where('platform', $publishing->platform)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function publishNow(SchedulePublishingJobRequest $request, Workspace $workspace, PublishingQueueItem $publishing, PublishingSchedulerService $scheduler): RedirectResponse
    {
        $this->ensureItemBelongsToWorkspace($workspace, $publishing);
        $account = $this->socialAccount($workspace, $request->validated('social_account_id'));
        $scheduler->publishNow($request->user(), $publishing, $account);

        return back()->with('status', 'Publishing job queued for immediate processing.');
    }

    public function scheduleJob(SchedulePublishingJobRequest $request, Workspace $workspace, PublishingQueueItem $publishing, PublishingSchedulerService $scheduler): RedirectResponse
    {
        $this->ensureItemBelongsToWorkspace($workspace, $publishing);
        $account = $this->socialAccount($workspace, $request->validated('social_account_id'));
        if ($request->validated('scheduled_at') === null) {
            throw ValidationException::withMessages(['scheduled_at' => 'A scheduled time is required.']);
        }
        $scheduler->schedule($request->user(), $publishing, $request->validated('scheduled_at'), $account);

        return back()->with('status', 'Publishing job scheduled.');
    }

    public function retryJob(PublishingQueueActionRequest $request, Workspace $workspace, PublishingQueueItem $publishing, PublishingJob $job, PublishingSchedulerService $scheduler): RedirectResponse
    {
        $this->ensureItemBelongsToWorkspace($workspace, $publishing);
        abort_unless($job->publishing_queue_item_id === $publishing->getKey(), 404);
        $this->authorize('manage', $publishing);
        $scheduler->retry($request->user(), $job);

        return back()->with('status', 'Publishing job queued for retry.');
    }

    public function cancelJob(PublishingQueueActionRequest $request, Workspace $workspace, PublishingQueueItem $publishing, PublishingJob $job, PublishingSchedulerService $scheduler): RedirectResponse
    {
        $this->ensureItemBelongsToWorkspace($workspace, $publishing);
        abort_unless($job->publishing_queue_item_id === $publishing->getKey(), 404);
        $this->authorize('manage', $publishing);
        $scheduler->cancel($request->user(), $job, $request->validated('comment'));

        return back()->with('status', 'Publishing job cancelled.');
    }

    public function cancel(PublishingQueueActionRequest $request, Workspace $workspace, PublishingQueueItem $publishing, PublishingQueueService $service): RedirectResponse
    {
        $this->ensureItemBelongsToWorkspace($workspace, $publishing);
        $this->authorize('cancel', $publishing);
        $service->cancel($request->user(), $publishing, $request->validated(), $request);

        return back()->with('status', 'Publishing queue item cancelled.');
    }

    public function retry(PublishingQueueActionRequest $request, Workspace $workspace, PublishingQueueItem $publishing, PublishingQueueService $service): RedirectResponse
    {
        $this->ensureItemBelongsToWorkspace($workspace, $publishing);
        $this->authorize('retry', $publishing);
        $service->retry($request->user(), $publishing, $request->validated(), $request);

        return back()->with('status', 'Publishing queue item retried.');
    }

    public function processing(PublishingQueueActionRequest $request, Workspace $workspace, PublishingQueueItem $publishing, PublishingQueueService $service): RedirectResponse
    {
        $this->ensureItemBelongsToWorkspace($workspace, $publishing);
        $this->authorize('manage', $publishing);
        $service->markProcessing($request->user(), $publishing, $request->validated(), $request);

        return back()->with('status', 'Publishing queue item marked processing.');
    }

    public function published(PublishingQueueActionRequest $request, Workspace $workspace, PublishingQueueItem $publishing, PublishingQueueService $service): RedirectResponse
    {
        $this->ensureItemBelongsToWorkspace($workspace, $publishing);
        $this->authorize('manage', $publishing);
        $service->markPublished($request->user(), $publishing, $request->validated(), $request);

        return back()->with('status', 'Publishing queue item marked published.');
    }

    public function failed(PublishingQueueActionRequest $request, Workspace $workspace, PublishingQueueItem $publishing, PublishingQueueService $service): RedirectResponse
    {
        $this->ensureItemBelongsToWorkspace($workspace, $publishing);
        $this->authorize('manage', $publishing);
        $service->markFailed($request->user(), $publishing, $request->validated(), $request);

        return back()->with('status', 'Publishing queue item marked failed.');
    }

    private function ensureItemBelongsToWorkspace(Workspace $workspace, PublishingQueueItem $item): void
    {
        abort_unless($item->workspace_id === $workspace->getKey(), 404);
    }

    private function socialAccount(Workspace $workspace, ?string $accountId): ?SocialAccount
    {
        if ($accountId === null) {
            return null;
        }

        return SocialAccount::query()->where('workspace_id', $workspace->getKey())->findOrFail($accountId);
    }
}
