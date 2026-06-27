<x-layouts.app title="Facebook Channel">
    <section class="mx-auto max-w-6xl px-6 py-10">
        <div class="flex flex-col gap-4 border-b border-white/10 pb-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-cyan-300">Channels > {{ $workspace->name }}</p>
                <h1 class="mt-3 text-3xl font-semibold text-white">Facebook Channel</h1>
                <p class="mt-2 max-w-2xl text-sm text-zinc-400">Connect Meta-managed Pages, check connection health, sync Page details, and publish text test posts.</p>
            </div>
            <a href="{{ route('channels.facebook.connect') }}" class="rounded-md bg-cyan-400 px-4 py-2 font-semibold text-zinc-950 hover:bg-cyan-300">
                Connect Facebook
            </a>
        </div>

        <form method="POST" action="{{ route('channels.facebook.settings') }}" class="mt-8 rounded-lg border border-white/10 bg-white/[0.03] p-5">
            @csrf
            <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-white">Facebook App Settings</h2>
                    <p class="mt-1 text-sm text-zinc-400">Saved per workspace. The App Secret is encrypted and never displayed after saving.</p>
                </div>
                @if ($settings)
                    <span class="rounded-full border border-emerald-400/30 bg-emerald-400/10 px-3 py-1 text-xs font-medium text-emerald-200">Configured</span>
                @else
                    <span class="rounded-full border border-amber-400/30 bg-amber-400/10 px-3 py-1 text-xs font-medium text-amber-200">Setup required</span>
                @endif
            </div>

            <div class="mt-5 grid gap-4 lg:grid-cols-3">
                <div>
                    <label for="app_id" class="text-sm font-medium text-zinc-200">Facebook App ID</label>
                    <input id="app_id" name="app_id" value="{{ old('app_id', $settings?->app_id) }}" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white outline-none transition focus:border-cyan-300">
                </div>
                <div>
                    <label for="app_secret" class="text-sm font-medium text-zinc-200">Facebook App Secret</label>
                    <input id="app_secret" name="app_secret" type="password" autocomplete="new-password" placeholder="{{ $settings ? 'Leave blank to keep saved secret' : '' }}" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white outline-none transition focus:border-cyan-300">
                </div>
                <div>
                    <label for="redirect_uri" class="text-sm font-medium text-zinc-200">Redirect URI</label>
                    <input id="redirect_uri" name="redirect_uri" value="{{ old('redirect_uri', $settings?->redirect_uri ?? route('channels.facebook.callback')) }}" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white outline-none transition focus:border-cyan-300">
                </div>
            </div>

            <div class="mt-5 flex justify-end">
                <button class="rounded-md border border-cyan-300/40 px-4 py-2 text-sm font-medium text-cyan-100 hover:bg-cyan-400/10">Save Facebook Settings</button>
            </div>
        </form>

        <div class="mt-8 grid gap-5">
            @forelse ($connections as $connection)
                @php
                    $statusClasses = [
                        \App\Models\FacebookConnection::CONNECTION_ACTIVE => 'border-emerald-400/30 bg-emerald-400/10 text-emerald-200',
                        \App\Models\FacebookConnection::CONNECTION_NEEDS_REFRESH => 'border-amber-400/30 bg-amber-400/10 text-amber-200',
                        \App\Models\FacebookConnection::CONNECTION_DISCONNECTED => 'border-zinc-400/30 bg-zinc-400/10 text-zinc-200',
                        \App\Models\FacebookConnection::CONNECTION_ERROR => 'border-rose-400/30 bg-rose-400/10 text-rose-200',
                    ][$connection->connection_status] ?? 'border-zinc-400/30 bg-zinc-400/10 text-zinc-200';
                @endphp
                <article class="rounded-lg border border-white/10 bg-white/[0.03] p-5">
                    <div class="grid gap-5 lg:grid-cols-[1fr_360px] lg:items-start">
                        <div class="flex gap-4">
                            <div class="h-16 w-16 shrink-0 overflow-hidden rounded-md border border-white/10 bg-zinc-900">
                                @if ($connection->page_avatar)
                                    <img src="{{ $connection->page_avatar }}" alt="{{ $connection->page_name }}" class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full w-full items-center justify-center text-xl font-semibold text-cyan-200">{{ str($connection->page_name)->substr(0, 1)->upper() }}</div>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-3">
                                    <h2 class="text-xl font-semibold text-white">{{ $connection->page_name }}</h2>
                                    <span class="rounded-full border px-3 py-1 text-xs font-medium {{ $statusClasses }}">
                                        {{ str($connection->connection_status)->replace('_', ' ')->title() }}
                                    </span>
                                </div>
                                <p class="mt-1 text-sm text-zinc-400">Page ID {{ $connection->page_id }}</p>
                                <p class="mt-2 text-sm text-zinc-300">
                                    Connected as {{ $connection->facebook_user_name ?? 'Facebook user not synced' }}
                                </p>

                                <dl class="mt-5 grid gap-4 text-sm text-zinc-400 sm:grid-cols-2">
                                    <div>
                                        <dt class="text-zinc-500">Category</dt>
                                        <dd class="mt-1 text-zinc-200">{{ $connection->page_category ?? 'Not synced' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-zinc-500">Verification</dt>
                                        <dd class="mt-1 text-zinc-200">{{ $connection->page_verification_status ?? 'Not synced' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-zinc-500">Followers</dt>
                                        <dd class="mt-1 text-zinc-200">{{ $connection->page_followers_count !== null ? number_format($connection->page_followers_count) : 'Not provided' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-zinc-500">Likes</dt>
                                        <dd class="mt-1 text-zinc-200">{{ $connection->page_likes_count !== null ? number_format($connection->page_likes_count) : 'Not provided' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-zinc-500">Last sync</dt>
                                        <dd class="mt-1 text-zinc-200">{{ $connection->last_synced_at?->diffForHumans() ?? 'Never' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-zinc-500">Last tested</dt>
                                        <dd class="mt-1 text-zinc-200">{{ $connection->last_tested_at?->diffForHumans() ?? 'Never' }}</dd>
                                    </div>
                                </dl>

                                @if ($connection->last_error)
                                    <p class="mt-4 rounded-md border border-rose-400/20 bg-rose-400/10 px-3 py-2 text-sm text-rose-100">{{ $connection->last_error }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="grid gap-3">
                            <div class="grid grid-cols-2 gap-3">
                                <form method="POST" action="{{ route('channels.facebook.test', $connection) }}">
                                    @csrf
                                    <button class="w-full rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 hover:bg-white/10">Test Connection</button>
                                </form>
                                <form method="POST" action="{{ route('channels.facebook.sync', $connection) }}">
                                    @csrf
                                    <button class="w-full rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 hover:bg-white/10">Sync Page Info</button>
                                </form>
                            </div>

                            <form method="POST" action="{{ route('channels.facebook.publish-test', $connection) }}" class="rounded-lg border border-white/10 bg-zinc-950/60 p-4">
                                @csrf
                                <p class="text-sm font-medium text-white">Test post</p>
                                <p class="mt-2 rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-zinc-300">Test post from JARVIS AI Marketing Studio</p>
                                <button type="submit" class="mt-3 w-full rounded-md border border-cyan-300/40 px-3 py-2 text-sm font-medium text-cyan-100 hover:bg-cyan-400/10">
                                    Publish test post
                                </button>
                            </form>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-lg border border-white/10 bg-white/[0.03] p-8 text-center">
                    <h2 class="text-xl font-semibold text-white">No Facebook Pages connected</h2>
                    <p class="mt-2 text-zinc-400">Save Facebook App settings, then connect Facebook to save Page access tokens for this workspace.</p>
                    <a href="{{ route('channels.facebook.connect') }}" class="mt-5 inline-flex rounded-md bg-cyan-400 px-4 py-2 font-semibold text-zinc-950 hover:bg-cyan-300">
                        Connect Facebook
                    </a>
                </div>
            @endforelse
        </div>

        <div class="mt-8">{{ $connections->links() }}</div>
    </section>
</x-layouts.app>
