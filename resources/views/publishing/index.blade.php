<x-layouts.app title="Publishing Queue">
    <section class="mx-auto max-w-6xl px-6 py-10">
        <div class="flex flex-col gap-4 border-b border-white/10 pb-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-cyan-300">{{ $workspace->name }}</p>
                <h1 class="mt-3 text-3xl font-semibold text-white">Publishing Queue</h1>
            </div>
            @can('create', [\App\Models\PublishingQueueItem::class, $workspace])
                <a href="{{ route('workspaces.publishing.create', $workspace) }}" class="rounded-md bg-cyan-400 px-4 py-2 font-semibold text-zinc-950 hover:bg-cyan-300">Schedule content</a>
            @endcan
        </div>

        <form method="GET" action="{{ route('workspaces.publishing.index', $workspace) }}" class="mt-8 grid gap-3 rounded-lg border border-white/10 bg-white/[0.03] p-4 md:grid-cols-6">
            <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search queue" class="rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white md:col-span-2">
            <select name="brand_id" class="rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white">
                <option value="">All brands</option>
                @foreach ($workspace->brands as $brand)
                    <option value="{{ $brand->id }}" @selected(($filters['brand_id'] ?? '') === $brand->id)>{{ $brand->name }}</option>
                @endforeach
            </select>
            <select name="status" class="rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white">
                <option value="">All statuses</option>
                @foreach (\App\Models\PublishingQueueItem::STATUSES as $status)
                    <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            <input name="platform" value="{{ $filters['platform'] ?? '' }}" placeholder="Platform" class="rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white">
            <button class="rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 hover:bg-white/10">Filter</button>
        </form>

        <div class="mt-8 grid gap-4">
            @forelse ($items as $item)
                <article class="rounded-lg border border-white/10 bg-white/[0.03] p-5">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-[0.16em] text-cyan-300">{{ $item->platform }}</p>
                            <h2 class="mt-2 text-lg font-semibold text-white">{{ $item->generatedContent->title }}</h2>
                            <p class="mt-1 text-sm text-zinc-400">{{ $item->brand->name }} | {{ ucfirst($item->status) }} | Priority {{ $item->priority }}</p>
                        </div>
                        <a href="{{ route('workspaces.publishing.show', [$workspace, $item]) }}" class="rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 hover:bg-white/10">Open</a>
                    </div>
                </article>
            @empty
                <div class="rounded-lg border border-white/10 bg-white/[0.03] p-8 text-center">
                    <h2 class="text-xl font-semibold text-white">No queue items yet</h2>
                    <p class="mt-2 text-zinc-400">Prepare approved content for future publishing.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">{{ $items->links() }}</div>
    </section>
</x-layouts.app>
