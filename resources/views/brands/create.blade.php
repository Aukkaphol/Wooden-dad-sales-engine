<x-layouts.app title="Create brand">
    <section class="mx-auto max-w-4xl px-6 py-10">
        <div class="border-b border-white/10 pb-6">
            <p class="text-sm font-medium uppercase tracking-[0.2em] text-cyan-300">{{ $workspace->name }}</p>
            <h1 class="mt-3 text-3xl font-semibold text-white">Create brand</h1>
        </div>

        <form method="POST" action="{{ route('workspaces.brands.store', $workspace) }}" enctype="multipart/form-data" class="mt-8 space-y-6">
            @csrf
            @include('brands.partials.form', ['brand' => null])

            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-md bg-cyan-400 px-4 py-2 font-semibold text-zinc-950 transition hover:bg-cyan-300">Create brand</button>
                <a href="{{ route('workspaces.brands.index', $workspace) }}" class="text-sm text-zinc-300 hover:text-white">Cancel</a>
            </div>
        </form>
    </section>
</x-layouts.app>
