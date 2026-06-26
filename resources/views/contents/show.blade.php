<x-layouts.app title="{{ $content->title }}">
    <section class="mx-auto max-w-6xl px-6 py-10">
        <div class="flex flex-col gap-4 border-b border-white/10 pb-6 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-cyan-300">{{ $workspace->name }} · {{ $content->brand->name }}</p>
                <h1 class="mt-3 text-3xl font-semibold text-white">{{ $content->title }}</h1>
                <p class="mt-2 text-zinc-400">{{ str($content->content_type)->replace('_', ' ')->title() }} · {{ $content->platform }} · v{{ $content->version }} · {{ ucfirst($content->status) }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <form method="POST" action="{{ route('workspaces.contents.duplicate', [$workspace, $content]) }}">@csrf<button class="rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 hover:bg-white/10">Duplicate</button></form>
                @can('update', $content)<a href="{{ route('workspaces.contents.edit', [$workspace, $content]) }}" class="rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 hover:bg-white/10">Edit</a>@endcan
                @can('delete', $content)<form method="POST" action="{{ route('workspaces.contents.destroy', [$workspace, $content]) }}">@csrf @method('DELETE')<button class="rounded-md border border-rose-300/30 px-3 py-2 text-sm text-rose-200 hover:bg-rose-400/10">Delete</button></form>@endcan
            </div>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-[1fr_360px]">
            <div class="space-y-6">
                <div class="rounded-lg border border-white/10 bg-white/[0.03] p-5">
                    <h2 class="text-lg font-semibold text-white">Generated content</h2>
                    <pre class="mt-4 whitespace-pre-wrap rounded-md bg-zinc-900 p-4 text-sm leading-6 text-zinc-200">{{ $content->generated_content }}</pre>
                </div>
                <div class="rounded-lg border border-white/10 bg-white/[0.03] p-5">
                    <h2 class="text-lg font-semibold text-white">Preview</h2>
                    <form method="POST" action="{{ route('workspaces.contents.preview', [$workspace, $content]) }}" class="mt-4 space-y-3">
                        @csrf
                        @foreach (($content->variables ?? []) as $key => $value)
                            <input name="variables[{{ $key }}]" value="{{ $value }}" class="w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white">
                        @endforeach
                        <button class="rounded-md bg-cyan-400 px-4 py-2 text-sm font-semibold text-zinc-950 hover:bg-cyan-300">Preview</button>
                    </form>
                    @if ($preview)
                        <pre class="mt-4 whitespace-pre-wrap rounded-md bg-zinc-900 p-4 text-sm leading-6 text-zinc-200">{{ $preview }}</pre>
                    @endif
                </div>
                <div class="rounded-lg border border-white/10 bg-white/[0.03] p-5">
                    <h2 class="text-lg font-semibold text-white">Version history</h2>
                    <div class="mt-4 divide-y divide-white/10">
                        @foreach ($content->versions->sortByDesc('version') as $version)
                            <div class="py-3 text-sm text-zinc-300">v{{ $version->version }} · {{ $version->title }} · {{ $version->created_at->format('Y-m-d H:i') }}</div>
                        @endforeach
                    </div>
                </div>
            </div>
            <aside class="space-y-6">
                <div class="rounded-lg border border-white/10 bg-white/[0.03] p-5">
                    <h2 class="text-lg font-semibold text-white">Workflow</h2>
                    <div class="mt-4 space-y-3">
                        @can('submitForReview', $content)
                            <form method="POST" action="{{ route('workspaces.contents.workflow.submit', [$workspace, $content]) }}" class="space-y-2">
                                @csrf
                                <textarea name="comment" rows="2" placeholder="Review note" class="w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white"></textarea>
                                <button class="w-full rounded-md bg-cyan-400 px-3 py-2 text-sm font-semibold text-zinc-950 hover:bg-cyan-300">Submit for review</button>
                            </form>
                        @endcan

                        @can('approve', $content)
                            <form method="POST" action="{{ route('workspaces.contents.workflow.approve', [$workspace, $content]) }}" class="space-y-2">
                                @csrf
                                <textarea name="reviewer_notes" rows="2" placeholder="Reviewer notes" class="w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white"></textarea>
                                <button class="w-full rounded-md border border-emerald-300/30 px-3 py-2 text-sm text-emerald-200 hover:bg-emerald-400/10">Approve</button>
                            </form>
                        @endcan

                        @can('reject', $content)
                            <form method="POST" action="{{ route('workspaces.contents.workflow.reject', [$workspace, $content]) }}" class="space-y-2">
                                @csrf
                                <textarea name="comment" rows="2" placeholder="Rejection comment" class="w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white"></textarea>
                                <button class="w-full rounded-md border border-rose-300/30 px-3 py-2 text-sm text-rose-200 hover:bg-rose-400/10">Reject</button>
                            </form>
                        @endcan

                        @can('returnWithComment', $content)
                            <form method="POST" action="{{ route('workspaces.contents.workflow.return', [$workspace, $content]) }}" class="space-y-2">
                                @csrf
                                <textarea name="comment" rows="2" placeholder="Return comment" class="w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white"></textarea>
                                <button class="w-full rounded-md border border-amber-300/30 px-3 py-2 text-sm text-amber-200 hover:bg-amber-400/10">Return with comment</button>
                            </form>
                        @endcan

                        @can('schedule', $content)
                            <form method="POST" action="{{ route('workspaces.contents.workflow.schedule', [$workspace, $content]) }}" class="space-y-2">
                                @csrf
                                <input type="datetime-local" name="scheduled_at" class="w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white">
                                <button class="w-full rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 hover:bg-white/10">Schedule</button>
                            </form>
                        @endcan

                        @can('publish', $content)
                            <form method="POST" action="{{ route('workspaces.contents.workflow.publish', [$workspace, $content]) }}">@csrf<button class="w-full rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 hover:bg-white/10">Mark published</button></form>
                        @endcan

                        @can('archive', $content)
                            <form method="POST" action="{{ route('workspaces.contents.workflow.archive', [$workspace, $content]) }}">@csrf<button class="w-full rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 hover:bg-white/10">Archive</button></form>
                        @endcan
                    </div>
                </div>

                <div class="rounded-lg border border-white/10 bg-white/[0.03] p-5">
                    <h2 class="text-lg font-semibold text-white">Source</h2>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div class="flex justify-between gap-4"><dt class="text-zinc-400">Prompt</dt><dd class="text-white">{{ $content->promptTemplate->title }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-zinc-400">Creator</dt><dd class="text-white">{{ $content->creator->name }}</dd></div>
                    </dl>
                </div>
                <div class="rounded-lg border border-white/10 bg-white/[0.03] p-5">
                    <h2 class="text-lg font-semibold text-white">Assets</h2>
                    <div class="mt-4 space-y-2">
                        @forelse ($content->assets as $asset)<p class="text-sm text-zinc-300">{{ $asset->name }}</p>@empty<p class="text-sm text-zinc-400">No assets attached.</p>@endforelse
                    </div>
                </div>

                <div class="rounded-lg border border-white/10 bg-white/[0.03] p-5">
                    <h2 class="text-lg font-semibold text-white">Approval history</h2>
                    <div class="mt-4 space-y-3">
                        @forelse ($content->approvalHistories->sortByDesc('decided_at') as $history)
                            <div class="border-b border-white/10 pb-3 text-sm last:border-b-0 last:pb-0">
                                <p class="font-medium text-white">{{ str($history->decision)->replace('_', ' ')->title() }}</p>
                                <p class="mt-1 text-zinc-400">{{ $history->previous_status }} → {{ $history->new_status }}</p>
                                <p class="mt-1 text-zinc-400">{{ $history->reviewer?->name ?? 'System' }} · {{ $history->decided_at->format('Y-m-d H:i') }}</p>
                                @if ($history->comment)<p class="mt-2 text-zinc-300">{{ $history->comment }}</p>@endif
                            </div>
                        @empty
                            <p class="text-sm text-zinc-400">No workflow actions yet.</p>
                        @endforelse
                    </div>
                </div>
            </aside>
        </div>
    </section>
</x-layouts.app>
