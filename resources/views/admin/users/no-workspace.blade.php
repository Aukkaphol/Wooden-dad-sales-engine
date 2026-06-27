<x-layouts.app title="Users Without Workspace">
    <section class="mx-auto max-w-7xl px-6 py-10">
        <div class="flex flex-col gap-4 border-b border-white/10 pb-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-cyan-300">System Admin</p>
                <h1 class="mt-3 text-3xl font-semibold text-white">Users Without Workspace</h1>
                <p class="mt-2 text-sm text-zinc-400">Assign newly registered users to an active workspace.</p>
            </div>
            <a href="{{ route('admin.workspaces.index') }}" class="rounded-md border border-white/10 px-4 py-2 text-sm text-zinc-100 hover:bg-white/10">Back to workspaces</a>
        </div>

        <div class="mt-8 overflow-hidden rounded-lg border border-white/10">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[920px] text-left text-sm">
                    <thead class="bg-white/[0.04] text-zinc-300">
                        <tr>
                            <th class="px-4 py-3 font-medium">Name</th>
                            <th class="px-4 py-3 font-medium">Email</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium">Assign</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse ($users as $user)
                            <tr>
                                <td class="px-4 py-3 font-medium text-white">{{ $user->name }}</td>
                                <td class="px-4 py-3 text-zinc-300">{{ $user->email }}</td>
                                <td class="px-4 py-3 text-zinc-300">{{ ucfirst($user->status) }}</td>
                                <td class="px-4 py-3">
                                    <form method="POST" action="{{ route('admin.users.assign-workspace', $user) }}" class="flex flex-wrap gap-2">
                                        @csrf
                                        <select name="workspace_id" class="min-w-48 rounded-md border border-white/10 bg-zinc-900 px-2 py-1 text-sm text-white">
                                            @foreach ($workspaces as $workspace)
                                                <option value="{{ $workspace->id }}">{{ $workspace->name }}</option>
                                            @endforeach
                                        </select>
                                        <select name="role" class="rounded-md border border-white/10 bg-zinc-900 px-2 py-1 text-sm text-white">
                                            @foreach ($roles as $role)
                                                <option value="{{ $role }}">{{ str($role)->title() }}</option>
                                            @endforeach
                                        </select>
                                        <button class="rounded-md border border-cyan-300/40 px-3 py-1 text-cyan-100 hover:bg-cyan-400/10">Assign</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-10 text-center text-zinc-400">Every user currently belongs to a workspace.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-8">{{ $users->links() }}</div>
    </section>
</x-layouts.app>
