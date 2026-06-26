<x-layouts.app title="Edit content draft">
    <section class="mx-auto max-w-4xl px-6 py-10">
        <div class="border-b border-white/10 pb-6">
            <p class="text-sm font-medium uppercase tracking-[0.2em] text-cyan-300">{{ $workspace->name }}</p>
            <h1 class="mt-3 text-3xl font-semibold text-white">Edit content draft</h1>
        </div>
        <form method="POST" action="{{ route('workspaces.contents.update', [$workspace, $content]) }}" class="mt-8 space-y-6">
            @csrf
            @method('PUT')
            @include('contents.partials.form', ['content' => $content])
            <div>
                <label for="generated_content" class="block text-sm font-medium text-zinc-200">Generated content</label>
                <textarea id="generated_content" name="generated_content" rows="10" required class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white">{{ old('generated_content', $content->generated_content) }}</textarea>
                @error('generated_content')<p class="mt-2 text-sm text-rose-300">{{ $message }}</p>@enderror
            </div>
            <div class="flex items-center gap-3">
                <button class="rounded-md bg-cyan-400 px-4 py-2 font-semibold text-zinc-950 hover:bg-cyan-300">Save version</button>
                <a href="{{ route('workspaces.contents.show', [$workspace, $content]) }}" class="text-sm text-zinc-300 hover:text-white">Cancel</a>
            </div>
        </form>
    </section>
</x-layouts.app>
