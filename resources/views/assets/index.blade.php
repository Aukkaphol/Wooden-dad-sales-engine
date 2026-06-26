<x-layouts.app title="Asset Library">
    <section class="mx-auto max-w-6xl px-6 py-10">
        <div class="flex flex-col gap-4 border-b border-white/10 pb-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-cyan-300">{{ $workspace->name }}</p>
                <h1 class="mt-3 text-3xl font-semibold text-white">Asset Library</h1>
            </div>
            @can('create', [\App\Models\Asset::class, $workspace])
                <a href="{{ route('workspaces.assets.create', $workspace) }}" class="inline-flex items-center justify-center rounded-md bg-cyan-400 px-4 py-2 font-semibold text-zinc-950 transition hover:bg-cyan-300">
                    Upload asset
                </a>
            @endcan
        </div>

        <form method="GET" action="{{ route('workspaces.assets.index', $workspace) }}" class="mt-8 grid gap-3 rounded-lg border border-white/10 bg-white/[0.03] p-4 md:grid-cols-6">
            <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search assets" class="rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white outline-none transition focus:border-cyan-300 md:col-span-2">
            <select name="brand_id" class="rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white outline-none transition focus:border-cyan-300">
                <option value="">All brands</option>
                @foreach ($workspace->brands as $brand)
                    <option value="{{ $brand->id }}" @selected(($filters['brand_id'] ?? '') === $brand->id)>{{ $brand->name }}</option>
                @endforeach
            </select>
            <select name="type" class="rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white outline-none transition focus:border-cyan-300">
                <option value="">All types</option>
                @foreach (\App\Models\Asset::TYPES as $type)
                    <option value="{{ $type }}" @selected(($filters['type'] ?? '') === $type)>{{ ucfirst($type) }}</option>
                @endforeach
            </select>
            <select name="status" class="rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white outline-none transition focus:border-cyan-300">
                <option value="">All statuses</option>
                @foreach (\App\Models\Asset::STATUSES as $status)
                    <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-md border border-white/10 px-3 py-2 text-sm font-medium text-zinc-100 transition hover:bg-white/10">Filter</button>
            <input name="category" value="{{ $filters['category'] ?? '' }}" placeholder="Category" class="rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white outline-none transition focus:border-cyan-300">
            <input name="tag" value="{{ $filters['tag'] ?? '' }}" placeholder="Tag" class="rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white outline-none transition focus:border-cyan-300">
        </form>

        <div class="mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($assets as $asset)
                <article class="rounded-lg border border-white/10 bg-white/[0.03] p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-[0.16em] text-cyan-300">{{ ucfirst($asset->type) }}</p>
                            <h2 class="mt-2 line-clamp-2 text-lg font-semibold text-white">{{ $asset->name }}</h2>
                            <p class="mt-1 text-sm text-zinc-400">{{ $asset->brand->name }} · {{ number_format($asset->size_bytes / 1024, 1) }} KB</p>
                        </div>
                        <span class="rounded-md border border-white/10 px-2 py-1 text-xs text-zinc-300">{{ ucfirst($asset->status) }}</span>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <a href="{{ route('workspaces.assets.show', [$workspace, $asset]) }}" class="rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 transition hover:bg-white/10">Open</a>
                        @can('update', $asset)
                            <a href="{{ route('workspaces.assets.edit', [$workspace, $asset]) }}" class="rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 transition hover:bg-white/10">Edit</a>
                        @endcan
                    </div>
                </article>
            @empty
                <div class="rounded-lg border border-white/10 bg-white/[0.03] p-8 text-center md:col-span-2 xl:col-span-3">
                    <h2 class="text-xl font-semibold text-white">No assets yet</h2>
                    <p class="mt-2 text-zinc-400">Upload images, video, audio, logos, documents, and templates for this workspace.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $assets->links() }}
        </div>
    </section>
</x-layouts.app>
