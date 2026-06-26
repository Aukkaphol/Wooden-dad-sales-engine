<x-layouts.app title="Workspaces">
    <section class="mx-auto max-w-6xl px-6 py-10">
        <div class="flex flex-col gap-4 border-b border-white/10 pb-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-cyan-300">Workspace Management</p>
                <h1 class="mt-3 text-3xl font-semibold text-white">Workspaces</h1>
            </div>
            <a href="{{ route('workspaces.create') }}" class="inline-flex items-center justify-center rounded-md bg-cyan-400 px-4 py-2 font-semibold text-zinc-950 transition hover:bg-cyan-300">
                New workspace
            </a>
        </div>

        <div class="mt-8 grid gap-4">
            @forelse ($workspaces as $workspace)
                <article class="rounded-lg border border-white/10 bg-white/[0.03] p-5">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-white">{{ $workspace->name }}</h2>
                            <p class="mt-1 text-sm text-zinc-400">{{ $workspace->industry ?? 'No industry set' }} · {{ $workspace->timezone }}</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <form method="POST" action="{{ route('workspaces.switch', $workspace) }}">
                                @csrf
                                <button type="submit" class="rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 transition hover:bg-white/10">Switch</button>
                            </form>
                            <a href="{{ route('workspaces.show', $workspace) }}" class="rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 transition hover:bg-white/10">Open</a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-lg border border-white/10 bg-white/[0.03] p-8 text-center">
                    <h2 class="text-xl font-semibold text-white">No workspaces yet</h2>
                    <p class="mt-2 text-zinc-400">Create the first workspace to start organizing brands, assets, and campaigns.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $workspaces->links() }}
        </div>
    </section>
</x-layouts.app>
