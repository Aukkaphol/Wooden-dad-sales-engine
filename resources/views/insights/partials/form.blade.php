@php
    $metadata = old('metadata', '');
    $metadata = is_array($metadata) ? json_encode($metadata, JSON_PRETTY_PRINT) : $metadata;
@endphp

<div class="grid gap-5 md:grid-cols-2">
    <label class="block">
        <span class="text-sm font-medium text-zinc-200">Generated content</span>
        <select name="generated_content_id" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white">
            @foreach ($workspace->generatedContents as $content)
                <option value="{{ $content->id }}" @selected(old('generated_content_id') === $content->id)>{{ $content->title }} - {{ $content->brand->name }}</option>
            @endforeach
        </select>
        @error('generated_content_id')<span class="mt-1 block text-sm text-red-300">{{ $message }}</span>@enderror
    </label>

    <label class="block">
        <span class="text-sm font-medium text-zinc-200">Analytics record</span>
        <select name="analytics_record_id" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white">
            <option value="">Manual insight</option>
            @foreach ($workspace->analyticsRecords as $record)
                <option value="{{ $record->id }}" @selected(old('analytics_record_id') === $record->id)>{{ $record->platform }} - {{ $record->generatedContent->title }} - Score {{ $record->score }}</option>
            @endforeach
        </select>
    </label>

    <label class="block">
        <span class="text-sm font-medium text-zinc-200">Insight type</span>
        <select name="insight_type" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white">
            @foreach (\App\Models\AiInsight::TYPES as $type)
                <option value="{{ $type }}" @selected(old('insight_type') === $type)>{{ str($type)->replace('_', ' ')->title() }}</option>
            @endforeach
        </select>
    </label>

    <label class="block">
        <span class="text-sm font-medium text-zinc-200">Priority</span>
        <select name="priority" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white">
            @foreach (\App\Models\AiInsight::PRIORITIES as $priority)
                <option value="{{ $priority }}" @selected(old('priority', 'medium') === $priority)>{{ ucfirst($priority) }}</option>
            @endforeach
        </select>
    </label>
</div>

<label class="block">
    <span class="text-sm font-medium text-zinc-200">Title</span>
    <input name="title" value="{{ old('title') }}" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white">
    @error('title')<span class="mt-1 block text-sm text-red-300">{{ $message }}</span>@enderror
</label>

<label class="block">
    <span class="text-sm font-medium text-zinc-200">Summary</span>
    <textarea name="summary" rows="4" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white">{{ old('summary') }}</textarea>
</label>

<label class="block">
    <span class="text-sm font-medium text-zinc-200">Recommendation</span>
    <textarea name="recommendation" rows="4" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white">{{ old('recommendation') }}</textarea>
</label>

<label class="block">
    <span class="text-sm font-medium text-zinc-200">Metadata JSON</span>
    <textarea name="metadata" rows="5" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 font-mono text-sm text-white">{{ $metadata }}</textarea>
    @error('metadata')<span class="mt-1 block text-sm text-red-300">{{ $message }}</span>@enderror
</label>
