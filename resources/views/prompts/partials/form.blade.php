<div class="grid gap-6 lg:grid-cols-2">
    <div>
        <label for="brand_id" class="block text-sm font-medium text-zinc-200">Brand</label>
        <select id="brand_id" name="brand_id" required class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white">
            <option value="">Select brand</option>
            @foreach ($workspace->brands as $brand)
                <option value="{{ $brand->id }}" @selected(old('brand_id', $prompt?->brand_id) === $brand->id)>{{ $brand->name }}</option>
            @endforeach
        </select>
        @error('brand_id')<p class="mt-2 text-sm text-rose-300">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="title" class="block text-sm font-medium text-zinc-200">Title</label>
        <input id="title" name="title" value="{{ old('title', $prompt?->title) }}" required class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white">
        @error('title')<p class="mt-2 text-sm text-rose-300">{{ $message }}</p>@enderror
    </div>
</div>

<div class="grid gap-6 lg:grid-cols-3">
    <div>
        <label for="category" class="block text-sm font-medium text-zinc-200">Category</label>
        <select id="category" name="category" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white">
            @foreach (\App\Models\PromptTemplate::CATEGORIES as $category)
                <option value="{{ $category }}" @selected(old('category', $prompt?->category) === $category)>{{ str($category)->replace('_', ' ')->title() }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="platform" class="block text-sm font-medium text-zinc-200">Platform</label>
        <select id="platform" name="platform" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white">
            @foreach (\App\Models\PromptTemplate::PLATFORMS as $platform)
                <option value="{{ $platform }}" @selected(old('platform', $prompt?->platform) === $platform)>{{ str($platform)->replace('_', ' ')->title() }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="status" class="block text-sm font-medium text-zinc-200">Status</label>
        <select id="status" name="status" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white">
            @foreach (\App\Models\PromptTemplate::STATUSES as $status)
                <option value="{{ $status }}" @selected(old('status', $prompt?->status ?? 'draft') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
    </div>
</div>

<div>
    <label for="prompt_template" class="block text-sm font-medium text-zinc-200">Prompt template</label>
    <textarea id="prompt_template" name="prompt_template" rows="8" required class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white">{{ old('prompt_template', $prompt?->prompt_template) }}</textarea>
    @error('prompt_template')<p class="mt-2 text-sm text-rose-300">{{ $message }}</p>@enderror
</div>

<div class="grid gap-6 lg:grid-cols-2">
    <div>
        <label for="variables" class="block text-sm font-medium text-zinc-200">Variables</label>
        <input id="variables" name="variables" value="{{ old('variables', $prompt ? implode(', ', $prompt->variables ?? []) : '') }}" placeholder="brand_name, topic, tone" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white">
        @error('variables')<p class="mt-2 text-sm text-rose-300">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="tags" class="block text-sm font-medium text-zinc-200">Tags</label>
        <input id="tags" name="tags" value="{{ old('tags', $prompt ? implode(', ', $prompt->tags ?? []) : '') }}" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white">
    </div>
</div>

<div class="grid gap-6 lg:grid-cols-2">
    <div>
        <label for="recommended_model" class="block text-sm font-medium text-zinc-200">Recommended model</label>
        <select id="recommended_model" name="recommended_model" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white">
            <option value="">No recommendation</option>
            @foreach (\App\Models\PromptTemplate::MODELS as $model)
                <option value="{{ $model }}" @selected(old('recommended_model', $prompt?->recommended_model) === $model)>{{ $model }}</option>
            @endforeach
        </select>
    </div>
    <label class="flex items-end gap-2 text-sm text-zinc-300">
        <input type="checkbox" name="favorite" value="1" @checked(old('favorite', $prompt?->favorite) )>
        Favorite
    </label>
</div>

<div>
    <label for="example_output" class="block text-sm font-medium text-zinc-200">Example output</label>
    <textarea id="example_output" name="example_output" rows="5" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white">{{ old('example_output', $prompt?->example_output) }}</textarea>
</div>
