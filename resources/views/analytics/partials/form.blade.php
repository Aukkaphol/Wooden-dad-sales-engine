@php
    $selectedContent = old('generated_content_id', $record?->generated_content_id);
    $selectedQueue = old('publishing_queue_item_id', $record?->publishing_queue_item_id);
    $audience = old('audience_breakdown', $record?->audience_breakdown ? json_encode($record->audience_breakdown, JSON_PRETTY_PRINT) : '');
    $metadata = old('metadata', $record?->metadata ? json_encode($record->metadata, JSON_PRETTY_PRINT) : '');
    $audience = is_array($audience) ? json_encode($audience, JSON_PRETTY_PRINT) : $audience;
    $metadata = is_array($metadata) ? json_encode($metadata, JSON_PRETTY_PRINT) : $metadata;
@endphp

<div class="grid gap-5 md:grid-cols-2">
    <label class="block">
        <span class="text-sm font-medium text-zinc-200">Generated content</span>
        <select name="generated_content_id" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white">
            @foreach ($workspace->generatedContents as $content)
                <option value="{{ $content->id }}" @selected($selectedContent === $content->id)>{{ $content->title }} - {{ $content->brand->name }}</option>
            @endforeach
        </select>
        @error('generated_content_id')<span class="mt-1 block text-sm text-red-300">{{ $message }}</span>@enderror
    </label>

    <label class="block">
        <span class="text-sm font-medium text-zinc-200">Publishing queue item</span>
        <select name="publishing_queue_item_id" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white">
            <option value="">Manual or imported data</option>
            @foreach ($workspace->publishingQueueItems as $item)
                <option value="{{ $item->id }}" @selected($selectedQueue === $item->id)>{{ $item->platform }} - {{ $item->generatedContent->title }}</option>
            @endforeach
        </select>
        @error('publishing_queue_item_id')<span class="mt-1 block text-sm text-red-300">{{ $message }}</span>@enderror
    </label>

    <label class="block">
        <span class="text-sm font-medium text-zinc-200">Platform</span>
        <input name="platform" value="{{ old('platform', $record?->platform) }}" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white">
        @error('platform')<span class="mt-1 block text-sm text-red-300">{{ $message }}</span>@enderror
    </label>

    <label class="block">
        <span class="text-sm font-medium text-zinc-200">Captured at</span>
        <input type="datetime-local" name="captured_at" value="{{ old('captured_at', optional($record?->captured_at)->format('Y-m-d\TH:i')) }}" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white">
        @error('captured_at')<span class="mt-1 block text-sm text-red-300">{{ $message }}</span>@enderror
    </label>

    <label class="block">
        <span class="text-sm font-medium text-zinc-200">Posted at</span>
        <input type="datetime-local" name="posted_at" value="{{ old('posted_at', optional($record?->posted_at)->format('Y-m-d\TH:i')) }}" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white">
    </label>
</div>

<div class="grid gap-4 md:grid-cols-5">
    @foreach (['views', 'reach', 'impressions', 'likes', 'comments', 'shares', 'saves', 'follows_gained', 'link_clicks', 'estimated_revenue', 'cost'] as $field)
        <label class="block">
            <span class="text-sm font-medium text-zinc-200">{{ str($field)->replace('_', ' ')->title() }}</span>
            <input type="number" step="{{ in_array($field, ['estimated_revenue', 'cost'], true) ? '0.01' : '1' }}" min="0" name="{{ $field }}" value="{{ old($field, $record?->{$field} ?? 0) }}" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white">
        </label>
    @endforeach
</div>

<label class="block">
    <span class="text-sm font-medium text-zinc-200">Audience breakdown JSON</span>
    <textarea name="audience_breakdown" rows="8" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 font-mono text-sm text-white">{{ $audience }}</textarea>
    @error('audience_breakdown')<span class="mt-1 block text-sm text-red-300">{{ $message }}</span>@enderror
</label>

<label class="block">
    <span class="text-sm font-medium text-zinc-200">Notes</span>
    <textarea name="notes" rows="4" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white">{{ old('notes', $record?->notes) }}</textarea>
</label>

<label class="block">
    <span class="text-sm font-medium text-zinc-200">Metadata JSON</span>
    <textarea name="metadata" rows="5" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 font-mono text-sm text-white">{{ $metadata }}</textarea>
    @error('metadata')<span class="mt-1 block text-sm text-red-300">{{ $message }}</span>@enderror
</label>
