<x-layouts.app title="Brands">
    <section class="mx-auto max-w-6xl px-6 py-10">
        <div class="flex flex-col gap-4 border-b border-white/10 pb-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-cyan-300">{{ $workspace->name }}</p>
                <h1 class="mt-3 text-3xl font-semibold text-white">Brands</h1>
            </div>
            @can('create', [\App\Models\Brand::class, $workspace])
                <a href="{{ route('workspaces.brands.create', $workspace) }}" class="inline-flex items-center justify-center rounded-md bg-cyan-400 px-4 py-2 font-semibold text-zinc-950 transition hover:bg-cyan-300">
                    New brand
                </a>
            @endcan
        </div>

        <div class="mt-8 grid gap-4 md:grid-cols-2">
            @forelse ($brands as $brand)
                <article class="rounded-lg border border-white/10 bg-white/[0.03] p-5">
                    <div class="flex gap-4">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-md border border-white/10 bg-zinc-900">
                            @if ($brand->logo_path)
                                <img src="{{ Storage::disk('public')->url($brand->logo_path) }}" alt="{{ $brand->name }} logo" class="h-full w-full object-cover">
                            @else
                                <span class="text-lg font-semibold text-cyan-300">{{ str($brand->name)->substr(0, 1)->upper() }}</span>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <h2 class="truncate text-lg font-semibold text-white">{{ $brand->name }}</h2>
                            <p class="mt-1 text-sm text-zinc-400">{{ $brand->tone ?? 'No tone set' }} · {{ $brand->font_family ?? 'No font set' }}</p>
                            <div class="mt-4 flex flex-wrap gap-2">
                                <a href="{{ route('workspaces.brands.show', [$workspace, $brand]) }}" class="rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 transition hover:bg-white/10">Open</a>
                                @can('update', $brand)
                                    <a href="{{ route('workspaces.brands.edit', [$workspace, $brand]) }}" class="rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 transition hover:bg-white/10">Edit</a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-lg border border-white/10 bg-white/[0.03] p-8 text-center md:col-span-2">
                    <h2 class="text-xl font-semibold text-white">No brands yet</h2>
                    <p class="mt-2 text-zinc-400">Create brand profiles for visual identity, voice, prompts, and default CTAs.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $brands->links() }}
        </div>
    </section>
</x-layouts.app>
