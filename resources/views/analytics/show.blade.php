<x-layouts.app title="Analytics record">
    <section class="mx-auto max-w-5xl px-6 py-10">
        <div class="flex flex-col gap-4 border-b border-white/10 pb-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-cyan-300">{{ $record->platform }} / {{ $record->brand->name }}</p>
                <h1 class="mt-3 text-3xl font-semibold text-white">{{ $record->generatedContent->title }}</h1>
            </div>
            <div class="flex gap-3">
                @can('update', $record)
                    <a href="{{ route('workspaces.analytics.edit', [$workspace, $record]) }}" class="rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 hover:bg-white/10">Edit</a>
                @endcan
                @can('delete', $record)
                    <form method="POST" action="{{ route('workspaces.analytics.destroy', [$workspace, $record]) }}">
                        @csrf
                        @method('DELETE')
                        <button class="rounded-md border border-red-400/30 px-3 py-2 text-sm text-red-200 hover:bg-red-400/10">Delete</button>
                    </form>
                @endcan
            </div>
        </div>

        <div class="mt-8 grid gap-4 md:grid-cols-4">
            @foreach (['views', 'reach', 'impressions', 'likes', 'comments', 'shares', 'saves', 'follows_gained', 'link_clicks'] as $field)
                <div class="rounded-lg border border-white/10 bg-white/[0.03] p-4">
                    <p class="text-xs uppercase tracking-[0.16em] text-zinc-500">{{ str($field)->replace('_', ' ')->title() }}</p>
                    <p class="mt-2 text-2xl font-semibold text-white">{{ number_format($record->{$field}) }}</p>
                </div>
            @endforeach
            <div class="rounded-lg border border-cyan-300/30 bg-cyan-300/10 p-4">
                <p class="text-xs uppercase tracking-[0.16em] text-cyan-200">Score</p>
                <p class="mt-2 text-2xl font-semibold text-white">{{ $record->score }}/100</p>
            </div>
        </div>

        <div class="mt-8 grid gap-6 md:grid-cols-2">
            <div class="rounded-lg border border-white/10 bg-white/[0.03] p-5">
                <h2 class="text-lg font-semibold text-white">Performance summary</h2>
                <p class="mt-3 text-sm text-zinc-300">{{ $record->score_reason }}</p>
                <p class="mt-3 text-sm text-cyan-100">{{ $record->recommendation }}</p>
                @if ($record->notes)
                    <p class="mt-4 text-sm text-zinc-400">{{ $record->notes }}</p>
                @endif
            </div>
            <div class="rounded-lg border border-white/10 bg-white/[0.03] p-5">
                <h2 class="text-lg font-semibold text-white">Audience breakdown</h2>
                <pre class="mt-3 overflow-auto rounded-md bg-zinc-950 p-4 text-xs text-zinc-200">{{ json_encode($record->audience_breakdown ?? [], JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>
    </section>
</x-layouts.app>
