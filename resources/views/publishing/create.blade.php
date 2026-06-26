<x-layouts.app title="Schedule publishing">
    <section class="mx-auto max-w-4xl px-6 py-10">
        <div class="border-b border-white/10 pb-6">
            <p class="text-sm font-medium uppercase tracking-[0.2em] text-cyan-300">{{ $workspace->name }}</p>
            <h1 class="mt-3 text-3xl font-semibold text-white">Schedule publishing</h1>
        </div>

        <form method="POST" action="{{ route('workspaces.publishing.store', $workspace) }}" class="mt-8 space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-medium text-zinc-200">Approved content</label>
                <select name="generated_content_id" required class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white">
                    <option value="">Select content</option>
                    @foreach ($workspace->generatedContents as $content)
                        <option value="{{ $content->id }}" @selected(old('generated_content_id') === $content->id)>{{ $content->title }} · {{ ucfirst($content->status) }}</option>
                    @endforeach
                </select>
                @error('generated_content_id')<p class="mt-2 text-sm text-rose-300">{{ $message }}</p>@enderror
            </div>
            <div class="grid gap-6 sm:grid-cols-3">
                <div><label class="block text-sm font-medium text-zinc-200">Platform</label><input name="platform" value="{{ old('platform', 'facebook') }}" required class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white"></div>
                <div><label class="block text-sm font-medium text-zinc-200">Scheduled at</label><input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white"></div>
                <div><label class="block text-sm font-medium text-zinc-200">Priority</label><input type="number" min="1" max="999" name="priority" value="{{ old('priority', 100) }}" required class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white"></div>
            </div>
            <div><label class="block text-sm font-medium text-zinc-200">Comment</label><textarea name="comment" rows="3" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white">{{ old('comment') }}</textarea></div>
            <div class="flex items-center gap-3">
                <button class="rounded-md bg-cyan-400 px-4 py-2 font-semibold text-zinc-950 hover:bg-cyan-300">Add to queue</button>
                <a href="{{ route('workspaces.publishing.index', $workspace) }}" class="text-sm text-zinc-300 hover:text-white">Cancel</a>
            </div>
        </form>
    </section>
</x-layouts.app>
