<x-layouts.app title="Sign in">
    <x-auth-card>
        <div>
            <h2 class="text-2xl font-semibold text-white">Sign in</h2>
            <p class="mt-2 text-sm text-zinc-400">Access your marketing operations console.</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-zinc-200">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white outline-none transition focus:border-cyan-300">
                @error('email')
                    <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-zinc-200">Password</label>
                <input id="password" name="password" type="password" required autocomplete="current-password" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white outline-none transition focus:border-cyan-300">
                @error('password')
                    <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <label class="flex items-center gap-3 text-sm text-zinc-300">
                <input name="remember" type="checkbox" value="1" class="h-4 w-4 rounded border-white/10 bg-zinc-900 text-cyan-400 focus:ring-cyan-300">
                Remember this device
            </label>

            <button type="submit" class="w-full rounded-md bg-cyan-400 px-4 py-2.5 font-semibold text-zinc-950 transition hover:bg-cyan-300">
                Sign in
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-zinc-400">
            New to JARVIS?
            <a href="{{ route('register') }}" class="font-medium text-cyan-300 hover:text-cyan-200">Create an account</a>
        </p>
    </x-auth-card>
</x-layouts.app>
