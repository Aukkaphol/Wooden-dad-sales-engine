<div class="grid gap-6 lg:grid-cols-2">
    <div class="space-y-5">
        <div>
            <label for="name" class="block text-sm font-medium text-zinc-200">Brand name</label>
            <input id="name" name="name" type="text" value="{{ old('name', $brand?->name) }}" required class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white outline-none transition focus:border-cyan-300">
            @error('name')<p class="mt-2 text-sm text-rose-300">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="logo" class="block text-sm font-medium text-zinc-200">Logo</label>
            <input id="logo" name="logo" type="file" accept="image/*" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-zinc-300 outline-none transition file:mr-3 file:rounded-md file:border-0 file:bg-cyan-400 file:px-3 file:py-1.5 file:font-semibold file:text-zinc-950 focus:border-cyan-300">
            @error('logo')<p class="mt-2 text-sm text-rose-300">{{ $message }}</p>@enderror
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="primary_color" class="block text-sm font-medium text-zinc-200">Primary color</label>
                <input id="primary_color" name="primary_color" type="text" value="{{ old('primary_color', $brand?->primary_color) }}" placeholder="#0f172a" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white outline-none transition focus:border-cyan-300">
                @error('primary_color')<p class="mt-2 text-sm text-rose-300">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="secondary_color" class="block text-sm font-medium text-zinc-200">Secondary color</label>
                <input id="secondary_color" name="secondary_color" type="text" value="{{ old('secondary_color', $brand?->secondary_color) }}" placeholder="#06b6d4" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white outline-none transition focus:border-cyan-300">
                @error('secondary_color')<p class="mt-2 text-sm text-rose-300">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label for="font_family" class="block text-sm font-medium text-zinc-200">Font</label>
            <input id="font_family" name="font_family" type="text" value="{{ old('font_family', $brand?->font_family) }}" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white outline-none transition focus:border-cyan-300">
            @error('font_family')<p class="mt-2 text-sm text-rose-300">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="tone" class="block text-sm font-medium text-zinc-200">Brand tone</label>
            <input id="tone" name="tone" type="text" value="{{ old('tone', $brand?->tone) }}" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white outline-none transition focus:border-cyan-300">
            @error('tone')<p class="mt-2 text-sm text-rose-300">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="default_cta" class="block text-sm font-medium text-zinc-200">Default CTA</label>
            <input id="default_cta" name="default_cta" type="text" value="{{ old('default_cta', $brand?->default_cta) }}" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white outline-none transition focus:border-cyan-300">
            @error('default_cta')<p class="mt-2 text-sm text-rose-300">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="space-y-5">
        <div>
            <label for="contact_email" class="block text-sm font-medium text-zinc-200">Contact email</label>
            <input id="contact_email" name="contact_information[email]" type="email" value="{{ old('contact_information.email', $brand?->contact_information['email'] ?? null) }}" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white outline-none transition focus:border-cyan-300">
            @error('contact_information.email')<p class="mt-2 text-sm text-rose-300">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="contact_phone" class="block text-sm font-medium text-zinc-200">Contact phone</label>
            <input id="contact_phone" name="contact_information[phone]" type="text" value="{{ old('contact_information.phone', $brand?->contact_information['phone'] ?? null) }}" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white outline-none transition focus:border-cyan-300">
            @error('contact_information.phone')<p class="mt-2 text-sm text-rose-300">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="contact_website" class="block text-sm font-medium text-zinc-200">Website</label>
            <input id="contact_website" name="contact_information[website]" type="url" value="{{ old('contact_information.website', $brand?->contact_information['website'] ?? null) }}" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white outline-none transition focus:border-cyan-300">
            @error('contact_information.website')<p class="mt-2 text-sm text-rose-300">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="contact_address" class="block text-sm font-medium text-zinc-200">Address</label>
            <textarea id="contact_address" name="contact_information[address]" rows="3" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white outline-none transition focus:border-cyan-300">{{ old('contact_information.address', $brand?->contact_information['address'] ?? null) }}</textarea>
            @error('contact_information.address')<p class="mt-2 text-sm text-rose-300">{{ $message }}</p>@enderror
        </div>
    </div>
</div>

<div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
    @foreach (['facebook', 'instagram', 'linkedin', 'tiktok'] as $network)
        <div>
            <label for="social_{{ $network }}" class="block text-sm font-medium capitalize text-zinc-200">{{ $network }}</label>
            <input id="social_{{ $network }}" name="social_links[{{ $network }}]" type="url" value="{{ old('social_links.'.$network, $brand?->social_links[$network] ?? null) }}" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white outline-none transition focus:border-cyan-300">
            @error('social_links.'.$network)<p class="mt-2 text-sm text-rose-300">{{ $message }}</p>@enderror
        </div>
    @endforeach
</div>

<div>
    <label for="voice" class="block text-sm font-medium text-zinc-200">Brand voice</label>
    <textarea id="voice" name="voice" rows="5" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white outline-none transition focus:border-cyan-300">{{ old('voice', $brand?->voice) }}</textarea>
    @error('voice')<p class="mt-2 text-sm text-rose-300">{{ $message }}</p>@enderror
</div>

<div>
    <label for="default_prompt" class="block text-sm font-medium text-zinc-200">Default prompt</label>
    <textarea id="default_prompt" name="default_prompt" rows="6" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white outline-none transition focus:border-cyan-300">{{ old('default_prompt', $brand?->default_prompt) }}</textarea>
    @error('default_prompt')<p class="mt-2 text-sm text-rose-300">{{ $message }}</p>@enderror
</div>
