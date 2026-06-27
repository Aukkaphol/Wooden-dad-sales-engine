<x-layouts.app title="Create Workspace">
    <section class="mx-auto flex min-h-[calc(100vh-88px)] max-w-3xl items-center px-6 py-10">
        <div class="w-full rounded-lg border border-white/10 bg-white/[0.03] p-6">
            <p class="text-sm font-medium uppercase tracking-[0.2em] text-cyan-300">Workspace Onboarding</p>
            <h1 class="mt-3 text-3xl font-semibold text-white">Create your first workspace</h1>
            <p class="mt-2 text-sm text-zinc-400">This workspace becomes your company home for brands, assets, content, channels, and team members.</p>

            <form method="POST" action="{{ route('onboarding.workspace.store') }}" class="mt-8 space-y-5">
                @csrf
                @include('workspaces.partials.form', ['workspace' => null])
                <button class="w-full rounded-md bg-cyan-400 px-4 py-2 font-semibold text-zinc-950 transition hover:bg-cyan-300">
                    Create workspace
                </button>
            </form>
        </div>
    </section>
</x-layouts.app>
