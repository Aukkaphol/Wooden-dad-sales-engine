<x-layouts.app title="Analytics Lite">
    <section class="mx-auto max-w-6xl px-6 py-10">
        <div class="flex flex-col gap-4 border-b border-white/10 pb-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-cyan-300">{{ $workspace->name }}</p>
                <h1 class="mt-3 text-3xl font-semibold text-white">Analytics Lite</h1>
            </div>
            @can('create', [\App\Models\AnalyticsRecord::class, $workspace])
                <a href="{{ route('workspaces.analytics.create', $workspace) }}" class="rounded-md bg-cyan-400 px-4 py-2 font-semibold text-zinc-950 hover:bg-cyan-300">Add record</a>
            @endcan
        </div>

        <form method="GET" action="{{ route('workspaces.analytics.index', $workspace) }}" class="mt-8 grid gap-3 rounded-lg border border-white/10 bg-white/[0.03] p-4 md:grid-cols-6">
            <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search analytics" class="rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white md:col-span-2">
            <select name="brand_id" class="rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white">
                <option value="">All brands</option>
                @foreach ($workspace->brands as $brand)
                    <option value="{{ $brand->id }}" @selected(($filters['brand_id'] ?? '') === $brand->id)>{{ $brand->name }}</option>
                @endforeach
            </select>
            <input name="platform" value="{{ $filters['platform'] ?? '' }}" placeholder="Platform" class="rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white">
            <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white">
            <button class="rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 hover:bg-white/10">Filter</button>
        </form>

        <div class="mt-8 grid gap-4">
            @forelse ($records as $record)
                <article class="rounded-lg border border-white/10 bg-white/[0.03] p-5">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-[0.16em] text-cyan-300">{{ $record->platform }} / {{ $record->brand->name }}</p>
                            <h2 class="mt-2 text-lg font-semibold text-white">{{ $record->generatedContent->title }}</h2>
                            <p class="mt-1 text-sm text-zinc-400">Views {{ number_format($record->views) }} | Engagement {{ $record->engagement_rate }}% | Clicks {{ number_format($record->link_clicks) }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="rounded-md border border-cyan-300/30 bg-cyan-300/10 px-3 py-2 text-sm font-semibold text-cyan-100">Score {{ $record->score }}</span>
                            <a href="{{ route('workspaces.analytics.show', [$workspace, $record]) }}" class="rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 hover:bg-white/10">Open</a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-lg border border-white/10 bg-white/[0.03] p-8 text-center">
                    <h2 class="text-xl font-semibold text-white">No analytics records yet</h2>
                    <p class="mt-2 text-zinc-400">Add manual performance data to start learning which content works.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">{{ $records->links() }}</div>
    </section>
</x-layouts.app>
