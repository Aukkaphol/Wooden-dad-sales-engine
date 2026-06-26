<x-layouts.app title="{{ $brand->name }}">
    <section class="mx-auto max-w-6xl px-6 py-10">
        <div class="flex flex-col gap-4 border-b border-white/10 pb-6 sm:flex-row sm:items-start sm:justify-between">
            <div class="flex gap-5">
                <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-md border border-white/10 bg-zinc-900">
                    @if ($brand->logo_path)
                        <img src="{{ Storage::disk('public')->url($brand->logo_path) }}" alt="{{ $brand->name }} logo" class="h-full w-full object-cover">
                    @else
                        <span class="text-2xl font-semibold text-cyan-300">{{ str($brand->name)->substr(0, 1)->upper() }}</span>
                    @endif
                </div>
                <div>
                    <p class="text-sm font-medium uppercase tracking-[0.2em] text-cyan-300">{{ $workspace->name }}</p>
                    <h1 class="mt-3 text-3xl font-semibold text-white">{{ $brand->name }}</h1>
                    <p class="mt-2 text-zinc-400">{{ $brand->tone ?? 'No tone set' }} · {{ $brand->font_family ?? 'No font set' }}</p>
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                @can('update', $brand)
                    <a href="{{ route('workspaces.brands.edit', [$workspace, $brand]) }}" class="rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 transition hover:bg-white/10">Edit</a>
                @endcan
                @can('delete', $brand)
                    <form method="POST" action="{{ route('workspaces.brands.destroy', [$workspace, $brand]) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-md border border-rose-300/30 px-3 py-2 text-sm text-rose-200 transition hover:bg-rose-400/10">Delete</button>
                    </form>
                @endcan
            </div>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-2">
            <div class="rounded-lg border border-white/10 bg-white/[0.03] p-5">
                <h2 class="text-lg font-semibold text-white">Identity</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex justify-between gap-4"><dt class="text-zinc-400">Primary color</dt><dd class="text-white">{{ $brand->primary_color ?? 'Not set' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-zinc-400">Secondary color</dt><dd class="text-white">{{ $brand->secondary_color ?? 'Not set' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-zinc-400">Font</dt><dd class="text-white">{{ $brand->font_family ?? 'Not set' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-zinc-400">CTA</dt><dd class="text-white">{{ $brand->default_cta ?? 'Not set' }}</dd></div>
                </dl>
            </div>

            <div class="rounded-lg border border-white/10 bg-white/[0.03] p-5">
                <h2 class="text-lg font-semibold text-white">Contact</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    @foreach (($brand->contact_information ?? []) as $label => $value)
                        <div class="flex justify-between gap-4"><dt class="capitalize text-zinc-400">{{ str_replace('_', ' ', $label) }}</dt><dd class="break-all text-white">{{ $value }}</dd></div>
                    @endforeach
                    @if (blank($brand->contact_information))
                        <p class="text-sm text-zinc-400">No contact information set.</p>
                    @endif
                </dl>
            </div>

            <div class="rounded-lg border border-white/10 bg-white/[0.03] p-5 lg:col-span-2">
                <h2 class="text-lg font-semibold text-white">Brand voice</h2>
                <p class="mt-4 whitespace-pre-line text-sm leading-6 text-zinc-300">{{ $brand->voice ?: 'No brand voice set.' }}</p>
            </div>

            <div class="rounded-lg border border-white/10 bg-white/[0.03] p-5 lg:col-span-2">
                <h2 class="text-lg font-semibold text-white">Default prompt</h2>
                <p class="mt-4 whitespace-pre-line text-sm leading-6 text-zinc-300">{{ $brand->default_prompt ?: 'No default prompt set.' }}</p>
            </div>
        </div>
    </section>
</x-layouts.app>
