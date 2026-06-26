<x-layouts.app title="Create prompt">
    <section class="mx-auto max-w-4xl px-6 py-10">
        <div class="border-b border-white/10 pb-6">
            <p class="text-sm font-medium uppercase tracking-[0.2em] text-cyan-300">{{ $workspace->name }}</p>
            <h1 class="mt-3 text-3xl font-semibold text-white">Create prompt</h1>
        </div>

        <form method="POST" action="{{ route('workspaces.prompts.store', $workspace) }}" class="mt-8 space-y-6">
            @csrf
            @include('prompts.partials.form', ['prompt' => null])
            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-md bg-cyan-400 px-4 py-2 font-semibold text-zinc-950 transition hover:bg-cyan-300">Create prompt</button>
                <a href="{{ route('workspaces.prompts.index', $workspace) }}" class="text-sm text-zinc-300 hover:text-white">Cancel</a>
            </div>
        </form>
    </section>
</x-layouts.app>
