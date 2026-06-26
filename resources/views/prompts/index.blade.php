<x-layouts.app title="Prompt Engine">
    <section class="mx-auto max-w-6xl px-6 py-10">
        <div class="flex flex-col gap-4 border-b border-white/10 pb-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-cyan-300">{{ $workspace->name }}</p>
                <h1 class="mt-3 text-3xl font-semibold text-white">Prompt Engine</h1>
            </div>
            @can('create', [\App\Models\PromptTemplate::class, $workspace])
                <a href="{{ route('workspaces.prompts.create', $workspace) }}" class="rounded-md bg-cyan-400 px-4 py-2 font-semibold text-zinc-950 transition hover:bg-cyan-300">New prompt</a>
            @endcan
        </div>

        <form method="GET" action="{{ route('workspaces.prompts.index', $workspace) }}" class="mt-8 grid gap-3 rounded-lg border border-white/10 bg-white/[0.03] p-4 md:grid-cols-6">
            <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search prompts" class="rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white md:col-span-2">
            <select name="brand_id" class="rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white">
                <option value="">All brands</option>
                @foreach ($workspace->brands as $brand)
                    <option value="{{ $brand->id }}" @selected(($filters['brand_id'] ?? '') === $brand->id)>{{ $brand->name }}</option>
                @endforeach
            </select>
            <select name="category" class="rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white">
                <option value="">All categories</option>
                @foreach (\App\Models\PromptTemplate::CATEGORIES as $category)
                    <option value="{{ $category }}" @selected(($filters['category'] ?? '') === $category)>{{ str($category)->replace('_', ' ')->title() }}</option>
                @endforeach
            </select>
            <select name="platform" class="rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white">
                <option value="">All platforms</option>
                @foreach (\App\Models\PromptTemplate::PLATFORMS as $platform)
                    <option value="{{ $platform }}" @selected(($filters['platform'] ?? '') === $platform)>{{ str($platform)->replace('_', ' ')->title() }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 transition hover:bg-white/10">Filter</button>
            <select name="status" class="rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white">
                <option value="">All statuses</option>
                @foreach (\App\Models\PromptTemplate::STATUSES as $status)
                    <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            <select name="model" class="rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white">
                <option value="">All models</option>
                @foreach (\App\Models\PromptTemplate::MODELS as $model)
                    <option value="{{ $model }}" @selected(($filters['model'] ?? '') === $model)>{{ $model }}</option>
                @endforeach
            </select>
            <input name="tag" value="{{ $filters['tag'] ?? '' }}" placeholder="Tag" class="rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white">
            <label class="flex items-center gap-2 rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-zinc-300">
                <input type="checkbox" name="favorite" value="1" @checked(($filters['favorite'] ?? '') !== '')>
                Favorites
            </label>
        </form>

        <div class="mt-8 grid gap-4 md:grid-cols-2">
            @forelse ($prompts as $prompt)
                <article class="rounded-lg border border-white/10 bg-white/[0.03] p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-[0.16em] text-cyan-300">{{ str($prompt->category)->replace('_', ' ')->title() }}</p>
                            <h2 class="mt-2 text-lg font-semibold text-white">{{ $prompt->title }}</h2>
                            <p class="mt-1 text-sm text-zinc-400">{{ $prompt->brand->name }} · v{{ $prompt->version }} · {{ $prompt->recommended_model ?? 'No model' }}</p>
                        </div>
                        <span class="text-lg text-cyan-300">{{ $prompt->favorite ? '*' : '' }}</span>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <a href="{{ route('workspaces.prompts.show', [$workspace, $prompt]) }}" class="rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 transition hover:bg-white/10">Open</a>
                        @can('update', $prompt)
                            <a href="{{ route('workspaces.prompts.edit', [$workspace, $prompt]) }}" class="rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 transition hover:bg-white/10">Edit</a>
                        @endcan
                    </div>
                </article>
            @empty
                <div class="rounded-lg border border-white/10 bg-white/[0.03] p-8 text-center md:col-span-2">
                    <h2 class="text-xl font-semibold text-white">No prompts yet</h2>
                    <p class="mt-2 text-zinc-400">Create reusable prompt templates for brands and platforms.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">{{ $prompts->links() }}</div>
    </section>
</x-layouts.app>
