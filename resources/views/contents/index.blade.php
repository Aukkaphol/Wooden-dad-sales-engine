<x-layouts.app title="Content Generator">
    <section class="mx-auto max-w-6xl px-6 py-10">
        <div class="flex flex-col gap-4 border-b border-white/10 pb-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-cyan-300">{{ $workspace->name }}</p>
                <h1 class="mt-3 text-3xl font-semibold text-white">Content Generator</h1>
            </div>
            @can('create', [\App\Models\GeneratedContent::class, $workspace])
                <a href="{{ route('workspaces.contents.create', $workspace) }}" class="rounded-md bg-cyan-400 px-4 py-2 font-semibold text-zinc-950 hover:bg-cyan-300">New draft</a>
            @endcan
        </div>

        <form method="GET" action="{{ route('workspaces.contents.index', $workspace) }}" class="mt-8 grid gap-3 rounded-lg border border-white/10 bg-white/[0.03] p-4 md:grid-cols-6">
            <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search drafts" class="rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white md:col-span-2">
            <select name="brand_id" class="rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white">
                <option value="">All brands</option>
                @foreach ($workspace->brands as $brand)<option value="{{ $brand->id }}" @selected(($filters['brand_id'] ?? '') === $brand->id)>{{ $brand->name }}</option>@endforeach
            </select>
            <select name="content_type" class="rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white">
                <option value="">All types</option>
                @foreach (\App\Models\GeneratedContent::TYPES as $type)<option value="{{ $type }}" @selected(($filters['content_type'] ?? '') === $type)>{{ str($type)->replace('_', ' ')->title() }}</option>@endforeach
            </select>
            <select name="status" class="rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white">
                <option value="">All statuses</option>
                @foreach (\App\Models\GeneratedContent::STATUSES as $status)<option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>@endforeach
            </select>
            <button class="rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 hover:bg-white/10">Filter</button>
        </form>

        <div class="mt-8 grid gap-4 md:grid-cols-2">
            @forelse ($contents as $content)
                <article class="rounded-lg border border-white/10 bg-white/[0.03] p-5">
                    <p class="text-xs font-medium uppercase tracking-[0.16em] text-cyan-300">{{ str($content->content_type)->replace('_', ' ')->title() }}</p>
                    <h2 class="mt-2 text-lg font-semibold text-white">{{ $content->title }}</h2>
                    <p class="mt-1 text-sm text-zinc-400">{{ $content->brand->name }} · {{ $content->platform }} · v{{ $content->version }} · {{ ucfirst($content->status) }}</p>
                    <div class="mt-4 flex gap-2">
                        <a href="{{ route('workspaces.contents.show', [$workspace, $content]) }}" class="rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 hover:bg-white/10">Open</a>
                        @can('update', $content)<a href="{{ route('workspaces.contents.edit', [$workspace, $content]) }}" class="rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 hover:bg-white/10">Edit</a>@endcan
                    </div>
                </article>
            @empty
                <div class="rounded-lg border border-white/10 bg-white/[0.03] p-8 text-center md:col-span-2">
                    <h2 class="text-xl font-semibold text-white">No content drafts yet</h2>
                    <p class="mt-2 text-zinc-400">Create drafts from a brand, prompt, variables, and assets.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">{{ $contents->links() }}</div>
    </section>
</x-layouts.app>
