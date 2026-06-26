<x-layouts.app title="AI insight">
    <section class="mx-auto max-w-4xl px-6 py-10">
        <div class="flex flex-col gap-4 border-b border-white/10 pb-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-cyan-300">{{ str($insight->insight_type)->replace('_', ' ')->title() }}</p>
                <h1 class="mt-3 text-3xl font-semibold text-white">{{ $insight->title }}</h1>
                <p class="mt-2 text-sm text-zinc-400">{{ $insight->brand->name }} | {{ $insight->generatedContent->title }}</p>
            </div>
            @can('delete', $insight)
                <form method="POST" action="{{ route('workspaces.insights.destroy', [$workspace, $insight]) }}">
                    @csrf
                    @method('DELETE')
                    <button class="rounded-md border border-red-400/30 px-3 py-2 text-sm text-red-200 hover:bg-red-400/10">Delete</button>
                </form>
            @endcan
        </div>

        <div class="mt-8 rounded-lg border border-white/10 bg-white/[0.03] p-6">
            <div class="flex flex-wrap gap-3 text-sm">
                <span class="rounded-md border border-white/10 px-3 py-1 text-zinc-200">Status {{ ucfirst($insight->status) }}</span>
                <span class="rounded-md border border-white/10 px-3 py-1 text-zinc-200">Priority {{ ucfirst($insight->priority) }}</span>
            </div>
            <p class="mt-6 text-zinc-200">{{ $insight->summary }}</p>
            @if ($insight->recommendation)
                <p class="mt-4 text-cyan-100">{{ $insight->recommendation }}</p>
            @endif
        </div>

        @can('update', $insight)
            <div class="mt-6 flex flex-wrap gap-3">
                @foreach ([\App\Models\AiInsight::STATUS_REVIEWED, \App\Models\AiInsight::STATUS_APPLIED, \App\Models\AiInsight::STATUS_IGNORED] as $status)
                    <form method="POST" action="{{ route('workspaces.insights.status', [$workspace, $insight]) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="{{ $status }}">
                        <button class="rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 hover:bg-white/10">Mark {{ ucfirst($status) }}</button>
                    </form>
                @endforeach
            </div>
        @endcan

        @if ($insight->metadata)
            <div class="mt-8 rounded-lg border border-white/10 bg-white/[0.03] p-5">
                <h2 class="text-lg font-semibold text-white">Metadata</h2>
                <pre class="mt-3 overflow-auto rounded-md bg-zinc-950 p-4 text-xs text-zinc-200">{{ json_encode($insight->metadata, JSON_PRETTY_PRINT) }}</pre>
            </div>
        @endif
    </section>
</x-layouts.app>
