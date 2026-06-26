<x-layouts.app title="Create account">
    <x-auth-card>
        <div>
            <h2 class="text-2xl font-semibold text-white">Create your account</h2>
            <p class="mt-2 text-sm text-zinc-400">Start with a secure user profile. Workspaces come next.</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-5">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-zinc-200">Name</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white outline-none transition focus:border-cyan-300">
                @error('name')
                    <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-zinc-200">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white outline-none transition focus:border-cyan-300">
                @error('email')
                    <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-zinc-200">Password</label>
                <input id="password" name="password" type="password" required autocomplete="new-password" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white outline-none transition focus:border-cyan-300">
                @error('password')
                    <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-zinc-200">Confirm password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white outline-none transition focus:border-cyan-300">
            </div>

            <button type="submit" class="w-full rounded-md bg-cyan-400 px-4 py-2.5 font-semibold text-zinc-950 transition hover:bg-cyan-300">
                Create account
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-zinc-400">
            Already have an account?
            <a href="{{ route('login') }}" class="font-medium text-cyan-300 hover:text-cyan-200">Sign in</a>
        </p>
    </x-auth-card>
</x-layouts.app>
