<div class="grid gap-6 lg:grid-cols-2">
    <div>
        <label for="brand_id" class="block text-sm font-medium text-zinc-200">Brand</label>
        <select id="brand_id" name="brand_id" required class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white">
            <option value="">Select brand</option>
            @foreach ($workspace->brands as $brand)<option value="{{ $brand->id }}" @selected(old('brand_id', $content?->brand_id) === $brand->id)>{{ $brand->name }}</option>@endforeach
        </select>
        @error('brand_id')<p class="mt-2 text-sm text-rose-300">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="prompt_template_id" class="block text-sm font-medium text-zinc-200">Prompt template</label>
        <select id="prompt_template_id" name="prompt_template_id" required class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white">
            <option value="">Select prompt</option>
            @foreach ($workspace->promptTemplates as $prompt)<option value="{{ $prompt->id }}" @selected(old('prompt_template_id', $content?->prompt_template_id) === $prompt->id)>{{ $prompt->title }}</option>@endforeach
        </select>
        @error('prompt_template_id')<p class="mt-2 text-sm text-rose-300">{{ $message }}</p>@enderror
    </div>
</div>
<div>
    <label for="title" class="block text-sm font-medium text-zinc-200">Title</label>
    <input id="title" name="title" value="{{ old('title', $content?->title) }}" required class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white">
    @error('title')<p class="mt-2 text-sm text-rose-300">{{ $message }}</p>@enderror
</div>
<div class="grid gap-6 lg:grid-cols-3">
    <div><label class="block text-sm font-medium text-zinc-200">Platform</label><input name="platform" value="{{ old('platform', $content?->platform ?? 'facebook') }}" required class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white"></div>
    <div>
        <label class="block text-sm font-medium text-zinc-200">Content type</label>
        <select name="content_type" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white">
            @foreach (\App\Models\GeneratedContent::TYPES as $type)<option value="{{ $type }}" @selected(old('content_type', $content?->content_type) === $type)>{{ str($type)->replace('_', ' ')->title() }}</option>@endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-zinc-200">Status</label>
        <select name="status" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white">
            <option value="draft" selected>Draft</option>
        </select>
    </div>
</div>
<div>
    <label class="block text-sm font-medium text-zinc-200">Assets</label>
    <select name="asset_ids[]" multiple class="mt-2 min-h-32 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white">
        @foreach ($workspace->assets as $asset)<option value="{{ $asset->id }}" @selected(in_array($asset->id, old('asset_ids', $content?->assets()->pluck('assets.id')->all() ?? []), true))>{{ $asset->name }}</option>@endforeach
    </select>
</div>
<div class="grid gap-6 lg:grid-cols-2">
    <div><label class="block text-sm font-medium text-zinc-200">Tags</label><input name="tags" value="{{ old('tags', $content ? implode(', ', $content->tags ?? []) : '') }}" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white"></div>
    <div><label class="block text-sm font-medium text-zinc-200">Topic variable</label><input name="variables[topic]" value="{{ old('variables.topic', $content?->variables['topic'] ?? '') }}" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white"></div>
</div>
<div>
    <label class="block text-sm font-medium text-zinc-200">Notes</label>
    <textarea name="notes" rows="4" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white">{{ old('notes', $content?->notes) }}</textarea>
</div>
