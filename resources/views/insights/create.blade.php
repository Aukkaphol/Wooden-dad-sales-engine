<x-layouts.app title="Create AI insight">
    <section class="mx-auto max-w-4xl px-6 py-10">
        <div class="border-b border-white/10 pb-6">
            <p class="text-sm font-medium uppercase tracking-[0.2em] text-cyan-300">{{ $workspace->name }}</p>
            <h1 class="mt-3 text-3xl font-semibold text-white">Create AI insight</h1>
        </div>
        <form method="POST" action="{{ route('workspaces.insights.store', $workspace) }}" class="mt-8 space-y-6">
            @csrf
            @include('insights.partials.form')
            <div class="flex items-center gap-3">
                <button class="rounded-md bg-cyan-400 px-4 py-2 font-semibold text-zinc-950 hover:bg-cyan-300">Save insight</button>
                <a href="{{ route('workspaces.insights.index', $workspace) }}" class="text-sm text-zinc-300 hover:text-white">Cancel</a>
            </div>
        </form>
    </section>
</x-layouts.app>
