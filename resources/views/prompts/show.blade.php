<x-layouts.app title="{{ $prompt->title }}">
    <section class="mx-auto max-w-6xl px-6 py-10">
        <div class="flex flex-col gap-4 border-b border-white/10 pb-6 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-cyan-300">{{ $workspace->name }} · {{ $prompt->brand->name }}</p>
                <h1 class="mt-3 text-3xl font-semibold text-white">{{ $prompt->title }}</h1>
                <p class="mt-2 text-zinc-400">{{ str($prompt->category)->replace('_', ' ')->title() }} · {{ str($prompt->platform)->replace('_', ' ')->title() }} · v{{ $prompt->version }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <form method="POST" action="{{ route('workspaces.prompts.favorite', [$workspace, $prompt]) }}">@csrf<button class="rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 hover:bg-white/10">{{ $prompt->favorite ? 'Unfavorite' : 'Favorite' }}</button></form>
                <form method="POST" action="{{ route('workspaces.prompts.duplicate', [$workspace, $prompt]) }}">@csrf<button class="rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 hover:bg-white/10">Duplicate</button></form>
                <form method="POST" action="{{ route('workspaces.prompts.used', [$workspace, $prompt]) }}">@csrf<button class="rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 hover:bg-white/10">Mark used</button></form>
                @can('update', $prompt)<a href="{{ route('workspaces.prompts.edit', [$workspace, $prompt]) }}" class="rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 hover:bg-white/10">Edit</a>@endcan
                @can('delete', $prompt)<form method="POST" action="{{ route('workspaces.prompts.destroy', [$workspace, $prompt]) }}">@csrf @method('DELETE')<button class="rounded-md border border-rose-300/30 px-3 py-2 text-sm text-rose-200 hover:bg-rose-400/10">Delete</button></form>@endcan
            </div>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-[1fr_360px]">
            <div class="space-y-6">
                <div class="rounded-lg border border-white/10 bg-white/[0.03] p-5">
                    <h2 class="text-lg font-semibold text-white">Prompt template</h2>
                    <pre class="mt-4 whitespace-pre-wrap rounded-md bg-zinc-900 p-4 text-sm leading-6 text-zinc-200">{{ $prompt->prompt_template }}</pre>
                </div>

                <div class="rounded-lg border border-white/10 bg-white/[0.03] p-5">
                    <h2 class="text-lg font-semibold text-white">Preview</h2>
                    <form method="POST" action="{{ route('workspaces.prompts.preview', [$workspace, $prompt]) }}" class="mt-4 space-y-3">
                        @csrf
                        @foreach (($prompt->variables ?? []) as $variable)
                            <input name="values[{{ $variable }}]" placeholder="{{ $variable }}" class="w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white">
                        @endforeach
                        <button class="rounded-md bg-cyan-400 px-4 py-2 text-sm font-semibold text-zinc-950 hover:bg-cyan-300">Preview prompt</button>
                    </form>
                    @if ($preview)
                        <pre class="mt-4 whitespace-pre-wrap rounded-md bg-zinc-900 p-4 text-sm leading-6 text-zinc-200">{{ $preview }}</pre>
                    @endif
                    @if ($missingVariables)
                        <p class="mt-3 text-sm text-rose-300">Missing: {{ implode(', ', $missingVariables) }}</p>
                    @endif
                </div>

                <div class="rounded-lg border border-white/10 bg-white/[0.03] p-5">
                    <h2 class="text-lg font-semibold text-white">Version history</h2>
                    <div class="mt-4 divide-y divide-white/10">
                        @foreach ($prompt->versions->sortByDesc('version') as $version)
                            <div class="py-3 text-sm text-zinc-300">v{{ $version->version }} · {{ $version->title }} · {{ $version->created_at->format('Y-m-d H:i') }}</div>
                        @endforeach
                    </div>
                </div>
            </div>

            <aside class="space-y-6">
                <div class="rounded-lg border border-white/10 bg-white/[0.03] p-5">
                    <h2 class="text-lg font-semibold text-white">Statistics</h2>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div class="flex justify-between gap-4"><dt class="text-zinc-400">Model</dt><dd class="text-white">{{ $prompt->recommended_model ?? 'Not set' }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-zinc-400">Usage</dt><dd class="text-white">{{ $prompt->usage_count }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-zinc-400">Success rate</dt><dd class="text-white">{{ $prompt->success_rate }}%</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-zinc-400">Rating</dt><dd class="text-white">{{ $prompt->rating_average }} / 5</dd></div>
                    </dl>
                    <form method="POST" action="{{ route('workspaces.prompts.rate', [$workspace, $prompt]) }}" class="mt-5 space-y-3">
                        @csrf
                        <select name="rating" class="w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white">
                            @for ($rating = 5; $rating >= 1; $rating--)
                                <option value="{{ $rating }}">{{ $rating }}</option>
                            @endfor
                        </select>
                        <label class="flex items-center gap-2 text-sm text-zinc-300"><input type="checkbox" name="successful" value="1"> Successful</label>
                        <button class="w-full rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 hover:bg-white/10">Rate</button>
                    </form>
                </div>

                <div class="rounded-lg border border-white/10 bg-white/[0.03] p-5">
                    <h2 class="text-lg font-semibold text-white">Variables</h2>
                    <div class="mt-4 flex flex-wrap gap-2">
                        @forelse (($prompt->variables ?? []) as $variable)
                            <span class="rounded-md border border-white/10 px-2 py-1 text-sm text-zinc-200">{{ $variable }}</span>
                        @empty
                            <p class="text-sm text-zinc-400">No variables.</p>
                        @endforelse
                    </div>
                </div>
            </aside>
        </div>
    </section>
</x-layouts.app>
