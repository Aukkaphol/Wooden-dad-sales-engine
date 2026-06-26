@if (session('status'))
    <div class="mx-auto mt-6 max-w-7xl px-6">
        <p class="rounded-md border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-200">{{ session('status') }}</p>
    </div>
@endif

@if ($errors->any())
    <div class="mx-auto mt-6 max-w-7xl px-6">
        <div class="rounded-md border border-red-400/20 bg-red-400/10 px-4 py-3 text-sm text-red-100">
            <p class="font-semibold">Please review the highlighted fields.</p>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
