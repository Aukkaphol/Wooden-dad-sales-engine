<x-layouts.app title="Media Pipeline">
    <section class="mx-auto max-w-6xl px-6 py-10">
        <div class="flex flex-col gap-4 border-b border-white/10 pb-6 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-cyan-300">{{ $pipeline->brand->name }}</p>
                <h1 class="mt-3 text-3xl font-semibold text-white">{{ $pipeline->generatedContent?->title ?? 'Media Pipeline' }}</h1>
                <p class="mt-2 text-zinc-400">Current stage: {{ str($pipeline->current_stage)->replace('_', ' ')->title() }} | Status: {{ str($pipeline->status)->replace('_', ' ')->title() }}</p>
            </div>
            <a href="{{ route('workspaces.pipeline.index', $workspace) }}" class="rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 hover:bg-white/10">Back to pipelines</a>
        </div>

        <div class="mt-8 grid gap-3 md:grid-cols-7">
            @foreach (\App\Models\MediaPipelineRun::STAGES as $stage)
                <div class="rounded-md border {{ $pipeline->current_stage === $stage ? 'border-cyan-300/50 bg-cyan-300/10 text-cyan-100' : 'border-white/10 bg-white/[0.03] text-zinc-400' }} p-3 text-center text-xs font-medium">
                    {{ str($stage)->replace('_', ' ')->title() }}
                </div>
            @endforeach
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-[1fr_360px]">
            <div class="space-y-6">
                <div class="rounded-lg border border-white/10 bg-white/[0.03] p-5">
                    <h2 class="text-lg font-semibold text-white">Workflow actions</h2>
                    <div class="mt-4 flex flex-wrap gap-3">
                        @can('manage', $pipeline)
                            <form method="POST" action="{{ route('workspaces.pipeline.approve', [$workspace, $pipeline]) }}">
                                @csrf
                                <button class="rounded-md bg-cyan-400 px-4 py-2 font-semibold text-zinc-950 hover:bg-cyan-300">Approve</button>
                            </form>
                            <form method="POST" action="{{ route('workspaces.pipeline.reject', [$workspace, $pipeline]) }}">
                                @csrf
                                <button class="rounded-md border border-red-400/30 px-4 py-2 text-sm text-red-200 hover:bg-red-400/10">Reject</button>
                            </form>
                            <form method="POST" action="{{ route('workspaces.pipeline.revision', [$workspace, $pipeline]) }}">
                                @csrf
                                <button class="rounded-md border border-white/10 px-4 py-2 text-sm text-zinc-100 hover:bg-white/10">Request revision</button>
                            </form>
                        @endcan
                    </div>
                </div>

                <div class="rounded-lg border border-white/10 bg-white/[0.03] p-5">
                    <h2 class="text-lg font-semibold text-white">Publishing</h2>
                    <form method="POST" action="{{ route('workspaces.pipeline.queue', [$workspace, $pipeline]) }}" class="mt-4 grid gap-3 md:grid-cols-4">
                        @csrf
                        <input name="platform" value="{{ $pipeline->generatedContent?->platform }}" class="rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white">
                        <input type="datetime-local" name="scheduled_at" class="rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white">
                        <input type="number" name="priority" value="100" min="1" max="999" class="rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white">
                        <button class="rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 hover:bg-white/10">Queue</button>
                    </form>
                    @if ($pipeline->publishingQueueItem)
                        <form method="POST" action="{{ route('workspaces.pipeline.publish', [$workspace, $pipeline]) }}" class="mt-3">
                            @csrf
                            <button class="rounded-md bg-cyan-400 px-4 py-2 font-semibold text-zinc-950 hover:bg-cyan-300">Mark published</button>
                        </form>
                        <form method="POST" action="{{ route('workspaces.pipeline.cancel', [$workspace, $pipeline]) }}" class="mt-3">
                            @csrf
                            <button class="rounded-md border border-red-400/30 px-4 py-2 text-sm text-red-200 hover:bg-red-400/10">Cancel queue</button>
                        </form>
                    @endif
                </div>

                @if ($pipeline->analyticsRecord)
                    <div class="rounded-lg border border-white/10 bg-white/[0.03] p-5">
                        <h2 class="text-lg font-semibold text-white">Analytics update</h2>
                        <form method="POST" action="{{ route('workspaces.pipeline.analytics', [$workspace, $pipeline]) }}" class="mt-4 grid gap-3 md:grid-cols-4">
                            @csrf
                            @method('PATCH')
                            @foreach (['views', 'reach', 'impressions', 'likes', 'comments', 'shares', 'saves', 'follows_gained', 'link_clicks'] as $field)
                                <input type="number" min="0" name="{{ $field }}" value="{{ $pipeline->analyticsRecord->{$field} }}" placeholder="{{ str($field)->replace('_', ' ')->title() }}" class="rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white">
                            @endforeach
                            <input type="number" min="0" step="0.01" name="estimated_revenue" value="{{ $pipeline->analyticsRecord->estimated_revenue }}" placeholder="Revenue" class="rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white">
                            <input type="number" min="0" step="0.01" name="cost" value="{{ $pipeline->analyticsRecord->cost }}" placeholder="Cost" class="rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white">
                            <button class="rounded-md bg-cyan-400 px-4 py-2 font-semibold text-zinc-950 hover:bg-cyan-300">Update analytics</button>
                        </form>
                    </div>
                @endif
            </div>

            <aside class="rounded-lg border border-white/10 bg-white/[0.03] p-5">
                <h2 class="text-lg font-semibold text-white">Workflow timeline</h2>
                <div class="mt-4 space-y-4">
                    @forelse ($pipeline->histories->sortByDesc('created_at') as $history)
                        <div class="border-l border-cyan-300/30 pl-3">
                            <p class="text-sm font-medium text-white">{{ $history->description }}</p>
                            <p class="mt-1 text-xs text-zinc-500">{{ str($history->event)->replace('_', ' ')->title() }} | {{ $history->created_at->diffForHumans() }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-zinc-500">No workflow history yet.</p>
                    @endforelse
                </div>
            </aside>
        </div>
    </section>
</x-layouts.app>
