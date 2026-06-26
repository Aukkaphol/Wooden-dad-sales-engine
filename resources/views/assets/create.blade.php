<x-layouts.app title="Upload asset">
    <section class="mx-auto max-w-4xl px-6 py-10">
        <div class="border-b border-white/10 pb-6">
            <p class="text-sm font-medium uppercase tracking-[0.2em] text-cyan-300">{{ $workspace->name }}</p>
            <h1 class="mt-3 text-3xl font-semibold text-white">Upload asset</h1>
        </div>

        <form method="POST" action="{{ route('workspaces.assets.store', $workspace) }}" enctype="multipart/form-data" class="mt-8 space-y-6">
            @csrf
            @include('assets.partials.form', ['asset' => null])

            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-md bg-cyan-400 px-4 py-2 font-semibold text-zinc-950 transition hover:bg-cyan-300">Upload asset</button>
                <a href="{{ route('workspaces.assets.index', $workspace) }}" class="text-sm text-zinc-300 hover:text-white">Cancel</a>
            </div>
        </form>
    </section>
</x-layouts.app>
