<x-layouts.app title="{{ $workspace->name }}">
    <section class="mx-auto max-w-6xl px-6 py-10">
        <div class="flex flex-col gap-4 border-b border-white/10 pb-6 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-cyan-300">Workspace</p>
                <h1 class="mt-3 text-3xl font-semibold text-white">{{ $workspace->name }}</h1>
                <p class="mt-2 text-zinc-400">{{ $workspace->industry ?? 'No industry set' }} · {{ $workspace->timezone }} · {{ strtoupper($workspace->default_language) }}</p>
            </div>

            <div class="flex flex-wrap gap-2">
                @can('update', $workspace)
                    <a href="{{ route('workspaces.brands.index', $workspace) }}" class="rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 transition hover:bg-white/10">Brands</a>
                    <a href="{{ route('workspaces.assets.index', $workspace) }}" class="rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 transition hover:bg-white/10">Assets</a>
                    <a href="{{ route('workspaces.prompts.index', $workspace) }}" class="rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 transition hover:bg-white/10">Prompts</a>
                    <a href="{{ route('workspaces.contents.index', $workspace) }}" class="rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 transition hover:bg-white/10">Content</a>
                    <a href="{{ route('workspaces.publishing.index', $workspace) }}" class="rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 transition hover:bg-white/10">Publishing</a>
                    <a href="{{ route('workspaces.analytics.index', $workspace) }}" class="rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 transition hover:bg-white/10">Analytics</a>
                    <a href="{{ route('workspaces.insights.index', $workspace) }}" class="rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 transition hover:bg-white/10">Insights</a>
                    <a href="{{ route('workspaces.pipeline.index', $workspace) }}" class="rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 transition hover:bg-white/10">Pipeline</a>
                    <a href="{{ route('workspaces.director.show', $workspace) }}" class="rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 transition hover:bg-white/10">Director</a>
                    <a href="{{ route('workspaces.edit', $workspace) }}" class="rounded-md border border-white/10 px-3 py-2 text-sm text-zinc-100 transition hover:bg-white/10">Edit</a>
                @endcan
                @can('delete', $workspace)
                    <form method="POST" action="{{ route('workspaces.destroy', $workspace) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-md border border-rose-300/30 px-3 py-2 text-sm text-rose-200 transition hover:bg-rose-400/10">Delete</button>
                    </form>
                @endcan
            </div>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-[1fr_360px]">
            <div>
                <h2 class="text-xl font-semibold text-white">Members</h2>
                <div class="mt-4 overflow-hidden rounded-lg border border-white/10">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-white/[0.04] text-zinc-300">
                            <tr>
                                <th class="px-4 py-3 font-medium">Name</th>
                                <th class="px-4 py-3 font-medium">Email</th>
                                <th class="px-4 py-3 font-medium">Role</th>
                                <th class="px-4 py-3 font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @foreach ($workspace->memberships as $membership)
                                <tr>
                                    <td class="px-4 py-3 text-white">{{ $membership->user->name }}</td>
                                    <td class="px-4 py-3 text-zinc-300">{{ $membership->user->email }}</td>
                                    <td class="px-4 py-3 text-zinc-300">{{ ucfirst($membership->role) }}</td>
                                    <td class="px-4 py-3">
                                        @can('manageMembers', $workspace)
                                            @if ($membership->role !== \App\Models\WorkspaceUser::ROLE_OWNER)
                                                <div class="flex flex-wrap gap-2">
                                                    <form method="POST" action="{{ route('workspaces.members.update', [$workspace, $membership->user]) }}" class="flex gap-2">
                                                        @csrf
                                                        @method('PUT')
                                                        <select name="role" class="rounded-md border border-white/10 bg-zinc-900 px-2 py-1 text-sm text-white">
                                                            @foreach (\App\Models\WorkspaceUser::ROLES as $role)
                                                                @continue($role === \App\Models\WorkspaceUser::ROLE_OWNER)
                                                                <option value="{{ $role }}" @selected($membership->role === $role)>{{ str($role)->replace('_', ' ')->title() }}</option>
                                                            @endforeach
                                                        </select>
                                                        <button type="submit" class="rounded-md border border-white/10 px-2 py-1 text-zinc-100 transition hover:bg-white/10">Save</button>
                                                    </form>
                                                    <form method="POST" action="{{ route('workspaces.members.destroy', [$workspace, $membership->user]) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="rounded-md border border-rose-300/30 px-2 py-1 text-rose-200 transition hover:bg-rose-400/10">Remove</button>
                                                    </form>
                                                </div>
                                            @else
                                                <span class="text-zinc-500">Locked</span>
                                            @endif
                                        @else
                                            <span class="text-zinc-500">View only</span>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @can('manageMembers', $workspace)
                <aside class="rounded-lg border border-white/10 bg-white/[0.03] p-5">
                    <h2 class="text-lg font-semibold text-white">Add member</h2>
                    <form method="POST" action="{{ route('workspaces.members.store', $workspace) }}" class="mt-5 space-y-4">
                        @csrf
                        <div>
                            <label for="email" class="block text-sm font-medium text-zinc-200">User email</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white outline-none transition focus:border-cyan-300">
                            @error('email')
                                <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="role" class="block text-sm font-medium text-zinc-200">Role</label>
                            <select id="role" name="role" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-white outline-none transition focus:border-cyan-300">
                                @foreach (\App\Models\WorkspaceUser::ROLES as $role)
                                    @continue($role === \App\Models\WorkspaceUser::ROLE_OWNER)
                                    <option value="{{ $role }}">{{ str($role)->replace('_', ' ')->title() }}</option>
                                @endforeach
                            </select>
                            @error('role')
                                <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="w-full rounded-md bg-cyan-400 px-4 py-2 font-semibold text-zinc-950 transition hover:bg-cyan-300">Add member</button>
                    </form>
                </aside>
            @endcan
        </div>
    </section>
</x-layouts.app>
