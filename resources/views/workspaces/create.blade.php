<x-layouts.app title="Create workspace">
    <section class="mx-auto max-w-3xl px-6 py-10">
        <div class="border-b border-white/10 pb-6">
            <p class="text-sm font-medium uppercase tracking-[0.2em] text-cyan-300">Workspace Management</p>
            <h1 class="mt-3 text-3xl font-semibold text-white">Create workspace</h1>
        </div>

        <form method="POST" action="{{ route('workspaces.store') }}" class="mt-8 space-y-5">
            @csrf
            @include('workspaces.partials.form', ['workspace' => null])

            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-md bg-cyan-400 px-4 py-2 font-semibold text-zinc-950 transition hover:bg-cyan-300">Create workspace</button>
                <a href="{{ route('workspaces.index') }}" class="text-sm text-zinc-300 hover:text-white">Cancel</a>
            </div>
        </form>
    </section>
</x-layouts.app>
