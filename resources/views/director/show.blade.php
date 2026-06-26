<x-layouts.app title="AI Director">
    <section class="mx-auto max-w-6xl px-6 py-10">
        <div class="flex flex-col gap-4 border-b border-white/10 pb-6 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-cyan-300">{{ $workspace->name }}</p>
                <h1 class="mt-3 text-3xl font-semibold text-white">AI Director</h1>
                <p class="mt-2 text-zinc-400">Provider-independent marketing decisions from your workspace data.</p>
            </div>
            <form method="GET" action="{{ route('workspaces.director.show', $workspace) }}" class="flex gap-3">
                <select name="brand_id" class="rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white">
                    <option value="">All brands</option>
                    @foreach ($workspace->brands as $brand)
                        <option value="{{ $brand->id }}" @selected(($filters['brand_id'] ?? '') === $brand->id)>{{ $brand->name }}</option>
                    @endforeach
                </select>
                <button class="rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 hover:bg-white/10">Filter</button>
            </form>
        </div>

        <div class="mt-8 grid gap-4 md:grid-cols-2">
            @foreach ($decisions as $decision)
                <article class="rounded-lg border border-white/10 bg-white/[0.03] p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-[0.16em] text-cyan-300">{{ str($decision->type)->replace('_', ' ')->title() }}</p>
                            <h2 class="mt-2 text-lg font-semibold text-white">{{ $decision->title }}</h2>
                        </div>
                        <span class="rounded-md border border-cyan-300/30 bg-cyan-300/10 px-3 py-1 text-sm font-semibold text-cyan-100">{{ $decision->confidence }}%</span>
                    </div>
                    <p class="mt-4 text-zinc-200">{{ $decision->recommendation }}</p>
                    <p class="mt-3 text-sm text-zinc-400">{{ $decision->reasoning }}</p>
                </article>
            @endforeach
        </div>
    </section>
</x-layouts.app>
