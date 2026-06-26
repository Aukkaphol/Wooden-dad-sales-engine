<x-layouts.app title="{{ $asset->name }}">
    <section class="mx-auto max-w-6xl px-6 py-10">
        <div class="flex flex-col gap-4 border-b border-white/10 pb-6 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-cyan-300">{{ $workspace->name }}</p>
                <h1 class="mt-3 text-3xl font-semibold text-white">{{ $asset->name }}</h1>
                <p class="mt-2 text-zinc-400">{{ $asset->brand->name }} · {{ ucfirst($asset->type) }} · {{ $asset->mime_type }}</p>
            </div>

            <div class="flex flex-wrap gap-2">
                @can('update', $asset)
                    <a href="{{ route('workspaces.assets.edit', [$workspace, $asset]) }}" class="rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 transition hover:bg-white/10">Edit</a>
                @endcan
                @can('delete', $asset)
                    <form method="POST" action="{{ route('workspaces.assets.destroy', [$workspace, $asset]) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-md border border-rose-300/30 px-3 py-2 text-sm text-rose-200 transition hover:bg-rose-400/10">Delete</button>
                    </form>
                @endcan
            </div>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-2">
            <div class="rounded-lg border border-white/10 bg-white/[0.03] p-5">
                <h2 class="text-lg font-semibold text-white">File details</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex justify-between gap-4"><dt class="text-zinc-400">Status</dt><dd class="text-white">{{ ucfirst($asset->status) }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-zinc-400">Category</dt><dd class="text-white">{{ $asset->category ?? 'Not set' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-zinc-400">Size</dt><dd class="text-white">{{ number_format($asset->size_bytes / 1024, 1) }} KB</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-zinc-400">Dimensions</dt><dd class="text-white">{{ $asset->width && $asset->height ? "{$asset->width} x {$asset->height}" : 'Not available' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-zinc-400">Duration</dt><dd class="text-white">{{ $asset->duration_seconds ? $asset->duration_seconds.' seconds' : 'Not available' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-zinc-400">Uploaded by</dt><dd class="text-white">{{ $asset->uploader->name }}</dd></div>
                </dl>
            </div>

            <div class="rounded-lg border border-white/10 bg-white/[0.03] p-5">
                <h2 class="text-lg font-semibold text-white">Tags</h2>
                <div class="mt-4 flex flex-wrap gap-2">
                    @forelse (($asset->tags ?? []) as $tag)
                        <span class="rounded-md border border-white/10 px-2 py-1 text-sm text-zinc-200">{{ $tag }}</span>
                    @empty
                        <p class="text-sm text-zinc-400">No tags set.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-lg border border-white/10 bg-white/[0.03] p-5 lg:col-span-2">
                <h2 class="text-lg font-semibold text-white">Storage</h2>
                <p class="mt-4 break-all text-sm text-zinc-300">{{ $asset->disk }}:{{ $asset->path }}</p>
            </div>
        </div>
    </section>
</x-layouts.app>
