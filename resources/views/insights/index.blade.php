<x-layouts.app title="AI Insights">
    <section class="mx-auto max-w-6xl px-6 py-10">
        <div class="flex flex-col gap-4 border-b border-white/10 pb-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-cyan-300">{{ $workspace->name }}</p>
                <h1 class="mt-3 text-3xl font-semibold text-white">AI Insights</h1>
            </div>
            @can('create', [\App\Models\AiInsight::class, $workspace])
                <a href="{{ route('workspaces.insights.create', $workspace) }}" class="rounded-md bg-cyan-400 px-4 py-2 font-semibold text-zinc-950 hover:bg-cyan-300">Create insight</a>
            @endcan
        </div>

        <form method="GET" action="{{ route('workspaces.insights.index', $workspace) }}" class="mt-8 grid gap-3 rounded-lg border border-white/10 bg-white/[0.03] p-4 md:grid-cols-6">
            <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search insights" class="rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white md:col-span-2">
            <select name="brand_id" class="rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white">
                <option value="">All brands</option>
                @foreach ($workspace->brands as $brand)
                    <option value="{{ $brand->id }}" @selected(($filters['brand_id'] ?? '') === $brand->id)>{{ $brand->name }}</option>
                @endforeach
            </select>
            <select name="insight_type" class="rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white">
                <option value="">All types</option>
                @foreach (\App\Models\AiInsight::TYPES as $type)
                    <option value="{{ $type }}" @selected(($filters['insight_type'] ?? '') === $type)>{{ str($type)->replace('_', ' ')->title() }}</option>
                @endforeach
            </select>
            <select name="status" class="rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white">
                <option value="">All statuses</option>
                @foreach (\App\Models\AiInsight::STATUSES as $status)
                    <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            <button class="rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 hover:bg-white/10">Filter</button>
        </form>

        <div class="mt-8 grid gap-4">
            @forelse ($insights as $insight)
                <article class="rounded-lg border border-white/10 bg-white/[0.03] p-5">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-[0.16em] text-cyan-300">{{ str($insight->insight_type)->replace('_', ' ')->title() }} / {{ ucfirst($insight->priority) }}</p>
                            <h2 class="mt-2 text-lg font-semibold text-white">{{ $insight->title }}</h2>
                            <p class="mt-1 text-sm text-zinc-400">{{ $insight->generatedContent->title }} | {{ ucfirst($insight->status) }}</p>
                        </div>
                        <a href="{{ route('workspaces.insights.show', [$workspace, $insight]) }}" class="rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 hover:bg-white/10">Open</a>
                    </div>
                </article>
            @empty
                <div class="rounded-lg border border-white/10 bg-white/[0.03] p-8 text-center">
                    <h2 class="text-xl font-semibold text-white">No insights yet</h2>
                    <p class="mt-2 text-zinc-400">Insights can be created manually or from rule-based analytics scoring.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">{{ $insights->links() }}</div>
    </section>
</x-layouts.app>
