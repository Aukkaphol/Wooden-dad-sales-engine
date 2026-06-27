<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'JARVIS AI Marketing Studio' }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-zinc-950 font-sans text-zinc-100 antialiased">
        <div class="min-h-screen">
            <header class="border-b border-white/10 bg-zinc-950/90">
                <div class="mx-auto flex max-w-7xl flex-col gap-4 px-6 py-4 lg:flex-row lg:items-center lg:justify-between">
                    <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="text-sm font-semibold tracking-wide text-white">
                        JARVIS AI Marketing Studio
                    </a>

                    <nav class="flex flex-wrap items-center gap-3 text-sm text-zinc-300">
                        @auth
                            @php
                                $availableWorkspaces = auth()->user()->workspaces()->orderBy('name')->get();
                                $currentWorkspaceId = auth()->user()->current_workspace_id;
                                $navWorkspace = $availableWorkspaces->firstWhere('id', $currentWorkspaceId) ?? $availableWorkspaces->first();
                            @endphp

                            @if ($availableWorkspaces->isNotEmpty())
                                <form method="POST" action="{{ url('/workspaces/'.$currentWorkspaceId.'/switch') }}">
                                    @csrf
                                    <select
                                        aria-label="Current workspace"
                                        class="rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white outline-none transition focus:border-cyan-300"
                                        onchange="if (this.value) { this.form.action = '/workspaces/' + this.value + '/switch'; this.form.submit(); }"
                                    >
                                        @foreach ($availableWorkspaces as $workspace)
                                            <option value="{{ $workspace->id }}" @selected($workspace->id === $currentWorkspaceId)>
                                                {{ $workspace->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            @endif

                            <a href="{{ route('dashboard') }}" class="transition hover:text-white">Dashboard</a>
                            <a href="{{ route('workspaces.index') }}" class="transition hover:text-white">Workspaces</a>
                            @if (auth()->user()->ownedWorkspaces()->exists())
                                <a href="{{ route('admin.workspaces.index') }}" class="transition hover:text-white">Admin</a>
                            @endif
                            @if ($navWorkspace)
                                <a href="{{ route('workspaces.assets.index', $navWorkspace) }}" class="transition hover:text-white">Assets</a>
                                <a href="{{ route('workspaces.prompts.index', $navWorkspace) }}" class="transition hover:text-white">Prompts</a>
                                <a href="{{ route('workspaces.contents.index', $navWorkspace) }}" class="transition hover:text-white">Content</a>
                                <a href="{{ route('workspaces.publishing.index', $navWorkspace) }}" class="transition hover:text-white">Publishing</a>
                                <a href="{{ route('workspaces.analytics.index', $navWorkspace) }}" class="transition hover:text-white">Analytics</a>
                                <a href="{{ route('workspaces.pipeline.index', $navWorkspace) }}" class="transition hover:text-white">Pipeline</a>
                                <a href="{{ route('workspaces.director.show', $navWorkspace) }}" class="transition hover:text-white">Director</a>
                                <a href="{{ route('channels.facebook.index') }}" class="transition hover:text-white">Channels</a>
                            @endif
                            <span>{{ auth()->user()->name }}</span>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="rounded-md border border-white/10 px-3 py-2 text-zinc-100 transition hover:bg-white/10">
                                    Sign out
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="transition hover:text-white">Sign in</a>
                            <a href="{{ route('register') }}" class="rounded-md bg-cyan-400 px-3 py-2 font-medium text-zinc-950 transition hover:bg-cyan-300">
                                Create account
                            </a>
                        @endauth
                    </nav>
                </div>
            </header>

            <main>
                <x-flash />
                {{ $slot }}
            </main>
        </div>
        <script>
            document.querySelectorAll('form').forEach((form) => {
                form.addEventListener('submit', () => {
                    form.querySelectorAll('button[type="submit"], button:not([type])').forEach((button) => {
                        if (!button.dataset.keepEnabled) {
                            button.disabled = true;
                            button.classList.add('opacity-60', 'cursor-wait');
                        }
                    });
                });
            });
        </script>
    </body>
</html>
