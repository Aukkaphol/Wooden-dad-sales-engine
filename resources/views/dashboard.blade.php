<x-layouts.app title="Studio Dashboard">
    <section class="mx-auto max-w-7xl px-6 py-10">
        <div class="flex flex-col gap-5 border-b border-white/10 pb-6 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-cyan-300">Jarvis AI Marketing Studio</p>
                <h1 class="mt-3 text-3xl font-semibold text-white">Studio Dashboard</h1>
                <p class="mt-2 text-zinc-400">{{ $workspace->name }} home screen</p>
            </div>

            <form method="GET" action="{{ route('dashboard') }}" class="grid gap-3 sm:grid-cols-3 lg:min-w-[680px]">
                <select name="workspace_id" class="rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white" onchange="this.form.submit()">
                    @foreach ($workspaces as $availableWorkspace)
                        <option value="{{ $availableWorkspace->id }}" @selected($workspace->id === $availableWorkspace->id)>{{ $availableWorkspace->name }}</option>
                    @endforeach
                </select>
                <select name="brand_id" class="rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white" onchange="this.form.submit()">
                    <option value="">All brands</option>
                    @foreach ($workspace->brands as $brand)
                        <option value="{{ $brand->id }}" @selected(($filters['brand_id'] ?? '') === $brand->id)>{{ $brand->name }}</option>
                    @endforeach
                </select>
                <input name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search studio" class="rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white">
            </form>
        </div>

        <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
            @foreach ($cards as $card)
                <div class="rounded-lg border border-white/10 bg-white/[0.03] p-4">
                    <p class="text-xs font-medium uppercase tracking-[0.16em] text-zinc-500">{{ $card['label'] }}</p>
                    <p class="mt-3 text-3xl font-semibold text-white">{{ number_format($card['total']) }}</p>
                    <div class="mt-3 flex gap-3 text-xs text-zinc-400">
                        <span>Today {{ number_format($card['today']) }}</span>
                        <span>Week {{ number_format($card['week']) }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        @if (! empty($searchResults))
            <div class="mt-8 rounded-lg border border-white/10 bg-white/[0.03] p-5">
                <h2 class="text-lg font-semibold text-white">Global search</h2>
                <div class="mt-4 grid gap-4 md:grid-cols-3">
                    @foreach ($searchResults as $group => $items)
                        <div>
                            <p class="text-sm font-medium text-cyan-200">{{ $group }}</p>
                            <div class="mt-2 space-y-2">
                                @forelse ($items as $item)
                                    <p class="truncate rounded-md bg-zinc-950 px-3 py-2 text-sm text-zinc-300">
                                        {{ $item->title ?? $item->name ?? $item->platform ?? 'Result' }}
                                    </p>
                                @empty
                                    <p class="text-sm text-zinc-500">No matches</p>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="mt-8 rounded-lg border border-white/10 bg-white/[0.03] p-5">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-lg font-semibold text-white">AI Director</h2>
                <a href="{{ route('workspaces.director.show', $workspace) }}" class="text-sm text-cyan-200 hover:text-cyan-100">Open director</a>
            </div>
            <div class="mt-4 grid gap-4 md:grid-cols-3">
                @foreach ($directorWidgets as $decision)
                    <div class="rounded-md bg-zinc-950 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <p class="text-xs font-medium uppercase tracking-[0.14em] text-zinc-500">{{ $decision->title }}</p>
                            <span class="text-xs font-semibold text-cyan-200">{{ $decision->confidence }}%</span>
                        </div>
                        <p class="mt-3 text-sm text-zinc-200">{{ $decision->recommendation }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-[1fr_360px]">
            <div class="rounded-lg border border-white/10 bg-white/[0.03] p-5">
                <div class="flex flex-col gap-4 border-b border-white/10 pb-4 md:flex-row md:items-center md:justify-between">
                    <h2 class="text-lg font-semibold text-white">Recent Generated Content</h2>
                    <form method="GET" action="{{ route('dashboard') }}" class="grid gap-2 md:grid-cols-3">
                        <input type="hidden" name="workspace_id" value="{{ $workspace->id }}">
                        @if ($filters['brand_id'] ?? null)
                            <input type="hidden" name="brand_id" value="{{ $filters['brand_id'] }}">
                        @endif
                        <input name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search content" class="rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white">
                        <select name="content_status" class="rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white">
                            <option value="">All statuses</option>
                            @foreach (\App\Models\GeneratedContent::STATUSES as $status)
                                <option value="{{ $status }}" @selected(($filters['content_status'] ?? '') === $status)>{{ str($status)->replace('_', ' ')->title() }}</option>
                            @endforeach
                        </select>
                        <button class="rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 hover:bg-white/10">Filter</button>
                    </form>
                </div>

                <div class="mt-4 overflow-hidden rounded-lg border border-white/10">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-white/[0.04] text-zinc-300">
                            <tr>
                                <th class="px-4 py-3 font-medium">Thumbnail</th>
                                <th class="px-4 py-3 font-medium">Brand</th>
                                <th class="px-4 py-3 font-medium">Platform</th>
                                <th class="px-4 py-3 font-medium">Status</th>
                                <th class="px-4 py-3 font-medium">Score</th>
                                <th class="px-4 py-3 font-medium">Created</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @forelse ($contents as $content)
                                @php
                                    $asset = $content->assets->first(fn ($asset) => $asset->thumbnail_path || $asset->path);
                                    $thumbnailPath = $asset?->thumbnail_path ?? $asset?->path;
                                    $score = $content->analyticsRecords->sortByDesc('captured_at')->first()?->score;
                                @endphp
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            @if ($thumbnailPath)
                                                <div class="h-12 w-16 shrink-0 rounded-md bg-zinc-800 bg-cover bg-center" style="background-image: url('{{ asset('storage/'.$thumbnailPath) }}')"></div>
                                            @else
                                                <div class="flex h-12 w-16 shrink-0 items-center justify-center rounded-md bg-zinc-800 text-xs text-zinc-500">None</div>
                                            @endif
                                            <span class="max-w-40 truncate text-white">{{ $content->title }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-white">{{ $content->brand->name }}</td>
                                    <td class="px-4 py-3 text-zinc-300">{{ $content->platform }}</td>
                                    <td class="px-4 py-3 text-zinc-300">{{ str($content->status)->replace('_', ' ')->title() }}</td>
                                    <td class="px-4 py-3 text-zinc-300">{{ $score ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-zinc-400">{{ $content->created_at->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-zinc-500">No generated content found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $contents->links() }}</div>
            </div>

            <div class="space-y-6">
                <div class="rounded-lg border border-white/10 bg-white/[0.03] p-5">
                    <h2 class="text-lg font-semibold text-white">Activity Timeline</h2>
                    <div class="mt-4 space-y-3">
                        @forelse ($activities as $activity)
                            <div class="border-l border-cyan-300/30 pl-3">
                                <p class="text-sm font-medium text-white">{{ $activity->description ?? str($activity->event)->replace('.', ' ')->title() }}</p>
                                <p class="mt-1 text-xs text-zinc-500">{{ $activity->event }} | {{ $activity->created_at->diffForHumans() }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-zinc-500">No activity yet.</p>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-lg border border-white/10 bg-white/[0.03] p-5">
                    <h2 class="text-lg font-semibold text-white">Analytics</h2>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        @foreach ([['Total Views', 'views'], ['Total Reach', 'reach'], ['Total Engagement', 'engagement'], ['Followers Gained', 'followers_gained']] as [$label, $key])
                            <div class="rounded-md bg-zinc-950 p-3">
                                <p class="text-xs uppercase tracking-[0.14em] text-zinc-500">{{ $label }}</p>
                                <p class="mt-2 text-xl font-semibold text-white">{{ number_format($analyticsSummary[$key]) }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-2">
            <div class="rounded-lg border border-white/10 bg-white/[0.03] p-5">
                <h2 class="text-lg font-semibold text-white">Publishing Queue</h2>
                <div class="mt-4 grid gap-3 sm:grid-cols-4">
                    @foreach ([\App\Models\PublishingQueueItem::STATUS_SCHEDULED, \App\Models\PublishingQueueItem::STATUS_PROCESSING, \App\Models\PublishingQueueItem::STATUS_PUBLISHED, \App\Models\PublishingQueueItem::STATUS_FAILED] as $status)
                        <div class="rounded-md bg-zinc-950 p-3">
                            <p class="text-xs uppercase tracking-[0.14em] text-zinc-500">{{ ucfirst($status) }}</p>
                            <p class="mt-2 text-xl font-semibold text-white">{{ number_format($publishingStatusCounts[$status] ?? 0) }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 space-y-3">
                    @forelse ($queueItems as $item)
                        <div class="rounded-md border border-white/10 bg-zinc-950 p-4">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="font-medium text-white">{{ $item->generatedContent->title }}</p>
                                    <p class="mt-1 text-sm text-zinc-400">{{ $item->platform }} | {{ ucfirst($item->status) }} | {{ optional($item->scheduled_at)->format('Y-m-d H:i') ?? 'No schedule' }}</p>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('workspaces.publishing.show', [$workspace, $item]) }}" class="rounded-md border border-white/10 px-3 py-2 text-xs text-zinc-100 hover:bg-white/10">View</a>
                                    <a href="{{ route('workspaces.contents.edit', [$workspace, $item->generatedContent]) }}" class="rounded-md border border-white/10 px-3 py-2 text-xs text-zinc-100 hover:bg-white/10">Edit</a>
                                    @can('retry', $item)
                                        <form method="POST" action="{{ route('workspaces.publishing.retry', [$workspace, $item]) }}">
                                            @csrf
                                            <button class="rounded-md border border-white/10 px-3 py-2 text-xs text-zinc-100 hover:bg-white/10">Retry</button>
                                        </form>
                                    @endcan
                                    @can('cancel', $item)
                                        <form method="POST" action="{{ route('workspaces.publishing.cancel', [$workspace, $item]) }}">
                                            @csrf
                                            <button class="rounded-md border border-white/10 px-3 py-2 text-xs text-zinc-100 hover:bg-white/10">Cancel</button>
                                        </form>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-zinc-500">No scheduled, processing, published, or failed queue items.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-lg border border-white/10 bg-white/[0.03] p-5">
                <h2 class="text-lg font-semibold text-white">AI Insights</h2>
                <div class="mt-4 space-y-3">
                    @forelse ($insights as $insight)
                        <div class="rounded-md border border-white/10 bg-zinc-950 p-4">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <p class="text-xs font-medium uppercase tracking-[0.16em] text-cyan-300">{{ ucfirst($insight->priority) }} | {{ ucfirst($insight->status) }}</p>
                                    <p class="mt-2 font-medium text-white">{{ $insight->title }}</p>
                                    <p class="mt-1 line-clamp-2 text-sm text-zinc-400">{{ $insight->recommendation }}</p>
                                </div>
                                @can('update', $insight)
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ([\App\Models\AiInsight::STATUS_REVIEWED => 'Review', \App\Models\AiInsight::STATUS_APPLIED => 'Applied', \App\Models\AiInsight::STATUS_IGNORED => 'Ignore'] as $status => $label)
                                            <form method="POST" action="{{ route('workspaces.insights.status', [$workspace, $insight]) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="{{ $status }}">
                                                <button class="rounded-md border border-white/10 px-3 py-2 text-xs text-zinc-100 hover:bg-white/10">{{ $label }}</button>
                                            </form>
                                        @endforeach
                                    </div>
                                @endcan
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-zinc-500">No insight recommendations yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
