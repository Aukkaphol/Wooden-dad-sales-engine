<x-layouts.app title="Media Pipeline">
    <section class="mx-auto max-w-7xl px-6 py-10">
        <div class="flex flex-col gap-4 border-b border-white/10 pb-6 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-cyan-300">{{ $workspace->name }}</p>
                <h1 class="mt-3 text-3xl font-semibold text-white">Media Pipeline</h1>
                <p class="mt-2 text-zinc-400">Asset Library -> Prompt Library -> Content -> Approval -> Queue -> Analytics -> Insights</p>
            </div>
        </div>

        @can('create', [\App\Models\MediaPipelineRun::class, $workspace])
            <form method="POST" action="{{ route('workspaces.pipeline.store', $workspace) }}" class="mt-8 rounded-lg border border-white/10 bg-white/[0.03] p-5">
                @csrf
                <h2 class="text-lg font-semibold text-white">Start workflow</h2>
                <div class="mt-5 grid gap-5 lg:grid-cols-2">
                    <label class="block">
                        <span class="text-sm font-medium text-zinc-200">Brand</span>
                        <select name="brand_id" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white">
                            @foreach ($workspace->brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-zinc-200">Prompt template</span>
                        <select name="prompt_template_id" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white">
                            @foreach ($workspace->promptTemplates as $prompt)
                                <option value="{{ $prompt->id }}">{{ $prompt->title }} v{{ $prompt->version }} - {{ $prompt->brand->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-zinc-200">Title</span>
                        <input name="title" value="{{ old('title') }}" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white">
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-zinc-200">Platform</span>
                        <input name="platform" value="{{ old('platform', 'facebook') }}" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white">
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-zinc-200">Content type</span>
                        <select name="content_type" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white">
                            @foreach (\App\Models\GeneratedContent::TYPES as $type)
                                <option value="{{ $type }}">{{ str($type)->replace('_', ' ')->title() }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-zinc-200">Topic variable</span>
                        <input name="variables[topic]" value="{{ old('variables.topic', 'Launch campaign') }}" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white">
                    </label>
                </div>

                <div class="mt-5">
                    <p class="text-sm font-medium text-zinc-200">Assets</p>
                    <div class="mt-3 grid gap-3 md:grid-cols-3">
                        @forelse ($workspace->assets as $asset)
                            <label class="flex items-center gap-3 rounded-md border border-white/10 bg-zinc-950 p-3 text-sm text-zinc-200">
                                <input type="checkbox" name="asset_ids[]" value="{{ $asset->id }}" class="rounded border-white/20 bg-zinc-900">
                                <span>{{ $asset->name }} - {{ $asset->brand->name }}</span>
                            </label>
                        @empty
                            <p class="text-sm text-zinc-500">No assets available yet.</p>
                        @endforelse
                    </div>
                </div>

                <div class="mt-5">
                    <button class="rounded-md bg-cyan-400 px-4 py-2 font-semibold text-zinc-950 hover:bg-cyan-300">Generate and submit</button>
                </div>
            </form>
        @endcan

        <form method="GET" action="{{ route('workspaces.pipeline.index', $workspace) }}" class="mt-8 grid gap-3 rounded-lg border border-white/10 bg-white/[0.03] p-4 md:grid-cols-4">
            <select name="brand_id" class="rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white">
                <option value="">All brands</option>
                @foreach ($workspace->brands as $brand)
                    <option value="{{ $brand->id }}" @selected(($filters['brand_id'] ?? '') === $brand->id)>{{ $brand->name }}</option>
                @endforeach
            </select>
            <select name="stage" class="rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white">
                <option value="">All stages</option>
                @foreach (\App\Models\MediaPipelineRun::STAGES as $stage)
                    <option value="{{ $stage }}" @selected(($filters['stage'] ?? '') === $stage)>{{ str($stage)->replace('_', ' ')->title() }}</option>
                @endforeach
            </select>
            <input name="status" value="{{ $filters['status'] ?? '' }}" placeholder="Status" class="rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white">
            <button class="rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 hover:bg-white/10">Filter</button>
        </form>

        <div class="mt-8 grid gap-4">
            @forelse ($pipelines as $pipeline)
                <article class="rounded-lg border border-white/10 bg-white/[0.03] p-5">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-[0.16em] text-cyan-300">{{ str($pipeline->current_stage)->replace('_', ' ')->title() }} / {{ str($pipeline->status)->replace('_', ' ')->title() }}</p>
                            <h2 class="mt-2 text-lg font-semibold text-white">{{ $pipeline->generatedContent?->title ?? 'Pipeline draft' }}</h2>
                            <p class="mt-1 text-sm text-zinc-400">{{ $pipeline->brand->name }} | Prompt v{{ $pipeline->prompt_version }}</p>
                        </div>
                        <a href="{{ route('workspaces.pipeline.show', [$workspace, $pipeline]) }}" class="rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 hover:bg-white/10">Open pipeline</a>
                    </div>
                </article>
            @empty
                <div class="rounded-lg border border-white/10 bg-white/[0.03] p-8 text-center">
                    <h2 class="text-xl font-semibold text-white">No pipeline runs yet</h2>
                    <p class="mt-2 text-zinc-400">Start with assets and a prompt template to create the first end-to-end workflow.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">{{ $pipelines->links() }}</div>
    </section>
</x-layouts.app>
