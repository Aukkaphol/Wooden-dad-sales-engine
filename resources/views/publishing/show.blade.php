<x-layouts.app title="Publishing queue item">
    <section class="mx-auto max-w-6xl px-6 py-10">
        <div class="flex flex-col gap-4 border-b border-white/10 pb-6 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-cyan-300">{{ $workspace->name }} | {{ $item->brand->name }}</p>
                <h1 class="mt-3 text-3xl font-semibold text-white">{{ $item->generatedContent->title }}</h1>
                <p class="mt-2 text-zinc-400">{{ $item->platform }} | {{ ucfirst($item->status) }} | Priority {{ $item->priority }}</p>
            </div>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-[1fr_360px]">
            <div class="rounded-lg border border-white/10 bg-white/[0.03] p-5">
                <h2 class="text-lg font-semibold text-white">Queue details</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex justify-between gap-4"><dt class="text-zinc-400">Scheduled</dt><dd class="text-white">{{ $item->scheduled_at?->format('Y-m-d H:i') ?? 'Not scheduled' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-zinc-400">Published</dt><dd class="text-white">{{ $item->published_at?->format('Y-m-d H:i') ?? 'Not published' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-zinc-400">Retries</dt><dd class="text-white">{{ $item->retry_count }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-zinc-400">Failure reason</dt><dd class="text-white">{{ $item->failure_reason ?? 'None' }}</dd></div>
                </dl>
            </div>

            <aside class="space-y-6">
                <div class="rounded-lg border border-white/10 bg-white/[0.03] p-5">
                    <h2 class="text-lg font-semibold text-white">Scheduler actions</h2>
                    <div class="mt-4 space-y-3">
                        @can('manage', $item)
                            <form method="POST" action="{{ route('workspaces.publishing.jobs.publish-now', [$workspace, $item]) }}" class="space-y-2 rounded-md border border-white/10 bg-zinc-950 p-3">
                                @csrf
                                <select name="social_account_id" class="w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white">
                                    <option value="">No connected account yet</option>
                                    @foreach ($socialAccounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->name }} ({{ $account->platform->label() }})</option>
                                    @endforeach
                                </select>
                                <button class="w-full rounded-md bg-cyan-400 px-3 py-2 text-sm font-semibold text-zinc-950 hover:bg-cyan-300">Publish now</button>
                            </form>
                            <form method="POST" action="{{ route('workspaces.publishing.jobs.schedule', [$workspace, $item]) }}" class="space-y-2 rounded-md border border-white/10 bg-zinc-950 p-3">
                                @csrf
                                <select name="social_account_id" class="w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white">
                                    <option value="">No connected account yet</option>
                                    @foreach ($socialAccounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->name }} ({{ $account->platform->label() }})</option>
                                    @endforeach
                                </select>
                                <input type="datetime-local" name="scheduled_at" class="w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white">
                                <button class="w-full rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 hover:bg-white/10">Schedule publishing job</button>
                            </form>
                        @endcan
                    </div>
                </div>

                <div class="rounded-lg border border-white/10 bg-white/[0.03] p-5">
                    <h2 class="text-lg font-semibold text-white">Queue actions</h2>
                    <div class="mt-4 space-y-3">
                        @can('cancel', $item)<form method="POST" action="{{ route('workspaces.publishing.cancel', [$workspace, $item]) }}">@csrf<button class="w-full rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 hover:bg-white/10">Cancel schedule</button></form>@endcan
                        @can('retry', $item)<form method="POST" action="{{ route('workspaces.publishing.retry', [$workspace, $item]) }}">@csrf<button class="w-full rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 hover:bg-white/10">Retry failed</button></form>@endcan
                        @can('manage', $item)
                            <form method="POST" action="{{ route('workspaces.publishing.processing', [$workspace, $item]) }}">@csrf<button class="w-full rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 hover:bg-white/10">Mark processing</button></form>
                            <form method="POST" action="{{ route('workspaces.publishing.published', [$workspace, $item]) }}">@csrf<button class="w-full rounded-md border border-emerald-300/30 px-3 py-2 text-sm text-emerald-200 hover:bg-emerald-400/10">Mark published</button></form>
                            <form method="POST" action="{{ route('workspaces.publishing.failed', [$workspace, $item]) }}">@csrf<textarea name="comment" rows="2" placeholder="Failure reason" class="mb-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white"></textarea><button class="w-full rounded-md border border-rose-300/30 px-3 py-2 text-sm text-rose-200 hover:bg-rose-400/10">Mark failed</button></form>
                        @endcan
                    </div>
                </div>
            </aside>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-2">
            <div class="rounded-lg border border-white/10 bg-white/[0.03] p-5">
                <h2 class="text-lg font-semibold text-white">Publishing jobs</h2>
                <div class="mt-4 grid gap-4">
                    @forelse ($item->publishingJobs->sortByDesc('created_at') as $job)
                        <article class="rounded-md border border-white/10 bg-zinc-950 p-4">
                            <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                                <div>
                                    <p class="font-medium text-white">{{ str($job->status)->replace('_', ' ')->title() }} | {{ $job->platform }}</p>
                                    <p class="mt-1 text-sm text-zinc-400">Scheduled {{ $job->scheduled_at?->format('Y-m-d H:i') ?? 'immediate' }} | Attempts {{ $job->attempts }}</p>
                                    @if ($job->failure_reason)<p class="mt-2 text-sm text-red-200">{{ $job->failure_reason }}</p>@endif
                                </div>
                                @can('manage', $item)
                                    <div class="flex flex-wrap gap-2">
                                        @if ($job->status === \App\Models\PublishingJob::STATUS_FAILED)
                                            <form method="POST" action="{{ route('workspaces.publishing.jobs.retry', [$workspace, $item, $job]) }}">@csrf<button class="rounded-md border border-white/10 px-3 py-2 text-xs text-zinc-100 hover:bg-white/10">Retry job</button></form>
                                        @endif
                                        @if (! in_array($job->status, [\App\Models\PublishingJob::STATUS_PUBLISHED, \App\Models\PublishingJob::STATUS_CANCELLED], true))
                                            <form method="POST" action="{{ route('workspaces.publishing.jobs.cancel', [$workspace, $item, $job]) }}">@csrf<button class="rounded-md border border-red-400/30 px-3 py-2 text-xs text-red-200 hover:bg-red-400/10">Cancel job</button></form>
                                        @endif
                                    </div>
                                @endcan
                            </div>
                            <div class="mt-4 border-t border-white/10 pt-3">
                                <h3 class="text-sm font-medium text-zinc-200">Publishing log</h3>
                                <div class="mt-2 space-y-2">
                                    @forelse ($job->logs->sortByDesc('created_at') as $log)
                                        <p class="text-sm text-zinc-400"><span class="text-zinc-200">{{ strtoupper($log->level) }}</span> {{ $log->event }} | {{ $log->message }}</p>
                                    @empty
                                        <p class="text-sm text-zinc-500">No job logs yet.</p>
                                    @endforelse
                                </div>
                            </div>
                        </article>
                    @empty
                        <p class="text-sm text-zinc-500">No publishing jobs created yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-lg border border-white/10 bg-white/[0.03] p-5">
                <h2 class="text-lg font-semibold text-white">Publishing history</h2>
                <div class="mt-4 space-y-3">
                    @forelse ($item->histories->sortByDesc('created_at') as $history)
                        <div class="border-b border-white/10 pb-3 text-sm last:border-b-0">
                            <p class="font-medium text-white">{{ str($history->event)->replace('_', ' ')->title() }}</p>
                            <p class="mt-1 text-zinc-400">{{ $history->previous_status ?? 'none' }} -> {{ $history->new_status ?? 'none' }}</p>
                            <p class="mt-1 text-zinc-400">{{ $history->actor?->name ?? 'System' }} | {{ $history->created_at->format('Y-m-d H:i') }}</p>
                            @if ($history->comment)<p class="mt-2 text-zinc-300">{{ $history->comment }}</p>@endif
                        </div>
                    @empty
                        <p class="text-sm text-zinc-400">No publishing history yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
