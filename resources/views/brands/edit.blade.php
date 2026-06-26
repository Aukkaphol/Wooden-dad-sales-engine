<x-layouts.app title="Edit brand">
    <section class="mx-auto max-w-4xl px-6 py-10">
        <div class="border-b border-white/10 pb-6">
            <p class="text-sm font-medium uppercase tracking-[0.2em] text-cyan-300">{{ $workspace->name }}</p>
            <h1 class="mt-3 text-3xl font-semibold text-white">Edit brand</h1>
        </div>

        <form method="POST" action="{{ route('workspaces.brands.update', [$workspace, $brand]) }}" enctype="multipart/form-data" class="mt-8 space-y-6">
            @csrf
            @method('PUT')
            @include('brands.partials.form', ['brand' => $brand])

            <div>
                <label for="status" class="block text-sm font-medium text-zinc-200">Status</label>
                <select id="status" name="status" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white outline-none transition focus:border-cyan-300">
                    <option value="active" @selected(old('status', $brand->status) === 'active')>Active</option>
                    <option value="archived" @selected(old('status', $brand->status) === 'archived')>Archived</option>
                </select>
                @error('status')
                    <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-md bg-cyan-400 px-4 py-2 font-semibold text-zinc-950 transition hover:bg-cyan-300">Save changes</button>
                <a href="{{ route('workspaces.brands.show', [$workspace, $brand]) }}" class="text-sm text-zinc-300 hover:text-white">Cancel</a>
            </div>
        </form>
    </section>
</x-layouts.app>
