<div>
    <label for="name" class="block text-sm font-medium text-zinc-200">Name</label>
    <input id="name" name="name" type="text" value="{{ old('name', $workspace?->name) }}" required class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white outline-none transition focus:border-cyan-300">
    @error('name')
        <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="industry" class="block text-sm font-medium text-zinc-200">Industry</label>
    <input id="industry" name="industry" type="text" value="{{ old('industry', $workspace?->industry) }}" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white outline-none transition focus:border-cyan-300">
    @error('industry')
        <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
    @enderror
</div>

<div class="grid gap-5 sm:grid-cols-2">
    <div>
        <label for="timezone" class="block text-sm font-medium text-zinc-200">Timezone</label>
        <input id="timezone" name="timezone" type="text" value="{{ old('timezone', $workspace?->timezone ?? 'UTC') }}" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white outline-none transition focus:border-cyan-300">
        @error('timezone')
            <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="default_language" class="block text-sm font-medium text-zinc-200">Default language</label>
        <input id="default_language" name="default_language" type="text" value="{{ old('default_language', $workspace?->default_language ?? 'en') }}" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white outline-none transition focus:border-cyan-300">
        @error('default_language')
            <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
        @enderror
    </div>
</div>
