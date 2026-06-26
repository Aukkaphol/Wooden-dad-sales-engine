<div class="grid gap-6 lg:grid-cols-2">
    <div>
        <label for="brand_id" class="block text-sm font-medium text-zinc-200">Brand</label>
        <select id="brand_id" name="brand_id" required class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white outline-none transition focus:border-cyan-300">
            <option value="">Select brand</option>
            @foreach ($workspace->brands as $brand)
                <option value="{{ $brand->id }}" @selected(old('brand_id', $asset?->brand_id) === $brand->id)>{{ $brand->name }}</option>
            @endforeach
        </select>
        @error('brand_id')<p class="mt-2 text-sm text-rose-300">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="status" class="block text-sm font-medium text-zinc-200">Status</label>
        <select id="status" name="status" required class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white outline-none transition focus:border-cyan-300">
            @foreach (\App\Models\Asset::STATUSES as $status)
                <option value="{{ $status }}" @selected(old('status', $asset?->status ?? 'draft') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        @error('status')<p class="mt-2 text-sm text-rose-300">{{ $message }}</p>@enderror
    </div>
</div>

<div>
    <label for="name" class="block text-sm font-medium text-zinc-200">Asset name</label>
    <input id="name" name="name" type="text" value="{{ old('name', $asset?->name) }}" required class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white outline-none transition focus:border-cyan-300">
    @error('name')<p class="mt-2 text-sm text-rose-300">{{ $message }}</p>@enderror
</div>

<div>
    <label for="file" class="block text-sm font-medium text-zinc-200">File</label>
    <input id="file" name="file" type="file" @if (! $asset) required @endif class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-zinc-300 outline-none transition file:mr-3 file:rounded-md file:border-0 file:bg-cyan-400 file:px-3 file:py-1.5 file:font-semibold file:text-zinc-950 focus:border-cyan-300">
    @if ($asset)
        <p class="mt-2 text-sm text-zinc-400">Leave empty to keep the current file.</p>
    @endif
    @error('file')<p class="mt-2 text-sm text-rose-300">{{ $message }}</p>@enderror
</div>

<div class="grid gap-6 lg:grid-cols-2">
    <div>
        <label for="category" class="block text-sm font-medium text-zinc-200">Category</label>
        <input id="category" name="category" type="text" value="{{ old('category', $asset?->category) }}" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white outline-none transition focus:border-cyan-300">
        @error('category')<p class="mt-2 text-sm text-rose-300">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="tags" class="block text-sm font-medium text-zinc-200">Tags</label>
        <input id="tags" name="tags" type="text" value="{{ old('tags', $asset ? implode(', ', $asset->tags ?? []) : '') }}" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white outline-none transition focus:border-cyan-300">
        @error('tags')<p class="mt-2 text-sm text-rose-300">{{ $message }}</p>@enderror
    </div>
</div>
