<x-layouts.app title="Manage {{ $workspace->name }} Members">
    <section class="mx-auto max-w-7xl px-6 py-10">
        <div class="flex flex-col gap-4 border-b border-white/10 pb-6 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-cyan-300">System Admin</p>
                <h1 class="mt-3 text-3xl font-semibold text-white">{{ $workspace->name }}</h1>
                <p class="mt-2 text-sm text-zinc-400">{{ $workspace->slug }} | Owner: {{ $workspace->owner?->name ?? 'Unassigned' }} | {{ ucfirst($workspace->status) }}</p>
            </div>
            <a href="{{ route('admin.workspaces.index') }}" class="rounded-md border border-white/10 px-4 py-2 text-sm text-zinc-100 hover:bg-white/10">Back to workspaces</a>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-[1fr_360px]">
            <div>
                <h2 class="text-xl font-semibold text-white">Members</h2>
                <div class="mt-4 overflow-hidden rounded-lg border border-white/10">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[920px] text-left text-sm">
                            <thead class="bg-white/[0.04] text-zinc-300">
                                <tr>
                                    <th class="px-4 py-3 font-medium">Name</th>
                                    <th class="px-4 py-3 font-medium">Email</th>
                                    <th class="px-4 py-3 font-medium">Role</th>
                                    <th class="px-4 py-3 font-medium">Joined</th>
                                    <th class="px-4 py-3 font-medium">Status</th>
                                    <th class="px-4 py-3 font-medium">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                @forelse ($workspace->memberships as $membership)
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-white">{{ $membership->user->name }}</td>
                                        <td class="px-4 py-3 text-zinc-300">{{ $membership->user->email }}</td>
                                        <td class="px-4 py-3 text-zinc-300">{{ str($membership->role)->replace('_', ' ')->title() }}</td>
                                        <td class="px-4 py-3 text-zinc-400">{{ $membership->joined_at?->toDateString() ?? 'Unknown' }}</td>
                                        <td class="px-4 py-3 text-zinc-300">{{ ucfirst($membership->user->status) }}</td>
                                        <td class="px-4 py-3">
                                            <div class="flex flex-wrap gap-2">
                                                <form method="POST" action="{{ route('admin.workspaces.members.update', [$workspace, $membership->user]) }}" class="flex gap-2">
                                                    @csrf
                                                    @method('PUT')
                                                    <select name="role" class="rounded-md border border-white/10 bg-zinc-900 px-2 py-1 text-sm text-white">
                                                        @foreach ($roles as $role)
                                                            <option value="{{ $role }}" @selected($membership->role === $role)>{{ str($role)->title() }}</option>
                                                        @endforeach
                                                    </select>
                                                    <button class="rounded-md border border-white/10 px-2 py-1 text-zinc-100 hover:bg-white/10">Save</button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.workspaces.members.destroy', [$workspace, $membership->user]) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="rounded-md border border-rose-300/30 px-2 py-1 text-rose-200 hover:bg-rose-400/10">Remove</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-10 text-center text-zinc-400">No members assigned to this workspace.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <aside class="rounded-lg border border-white/10 bg-white/[0.03] p-5">
                <h2 class="text-lg font-semibold text-white">Add or update member</h2>
                <form method="POST" action="{{ route('admin.workspaces.members.store', $workspace) }}" class="mt-5 space-y-4">
                    @csrf
                    <div>
                        <label for="email" class="text-sm font-medium text-zinc-200">User email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white outline-none transition focus:border-cyan-300">
                        @error('email')<p class="mt-2 text-sm text-rose-300">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="role" class="text-sm font-medium text-zinc-200">Role</label>
                        <select id="role" name="role" class="mt-2 w-full rounded-md border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-white outline-none transition focus:border-cyan-300">
                            @foreach ($roles as $role)
                                <option value="{{ $role }}">{{ str($role)->title() }}</option>
                            @endforeach
                        </select>
                        @error('role')<p class="mt-2 text-sm text-rose-300">{{ $message }}</p>@enderror
                    </div>
                    <button class="w-full rounded-md bg-cyan-400 px-4 py-2 font-semibold text-zinc-950 hover:bg-cyan-300">Save member</button>
                </form>
            </aside>
        </div>
    </section>
</x-layouts.app>
