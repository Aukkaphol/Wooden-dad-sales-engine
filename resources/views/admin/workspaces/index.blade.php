<x-layouts.app title="Admin Workspaces">
    <section class="mx-auto max-w-7xl px-6 py-10">
        <div class="flex flex-col gap-4 border-b border-white/10 pb-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-cyan-300">System Admin</p>
                <h1 class="mt-3 text-3xl font-semibold text-white">Workspace Management</h1>
                <p class="mt-2 text-sm text-zinc-400">Manage workspace membership without Tinker, SQL, or SSH.</p>
            </div>
            <a href="{{ route('admin.users.no-workspace') }}" class="rounded-md border border-white/10 px-4 py-2 text-sm text-zinc-100 hover:bg-white/10">
                Users without workspace
            </a>
        </div>

        <div class="mt-8 overflow-hidden rounded-lg border border-white/10">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-left text-sm">
                    <thead class="bg-white/[0.04] text-zinc-300">
                        <tr>
                            <th class="px-4 py-3 font-medium">Name</th>
                            <th class="px-4 py-3 font-medium">Slug</th>
                            <th class="px-4 py-3 font-medium">Owner</th>
                            <th class="px-4 py-3 font-medium">Members</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium">Created</th>
                            <th class="px-4 py-3 font-medium">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse ($workspaces as $workspace)
                            <tr>
                                <td class="px-4 py-3 font-medium text-white">{{ $workspace->name }}</td>
                                <td class="px-4 py-3 font-mono text-zinc-300">{{ $workspace->slug }}</td>
                                <td class="px-4 py-3 text-zinc-300">{{ $workspace->owner?->name ?? 'Unassigned' }}</td>
                                <td class="px-4 py-3 text-zinc-300">{{ $workspace->members_count }}</td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full border border-emerald-400/30 bg-emerald-400/10 px-3 py-1 text-xs font-medium text-emerald-200">{{ ucfirst($workspace->status) }}</span>
                                </td>
                                <td class="px-4 py-3 text-zinc-400">{{ $workspace->created_at->toDateString() }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.workspaces.members', $workspace) }}" class="rounded-md border border-cyan-300/40 px-3 py-2 text-sm text-cyan-100 hover:bg-cyan-400/10">
                                        View / Manage
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center text-zinc-400">No workspaces found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-8">{{ $workspaces->links() }}</div>
    </section>
</x-layouts.app>
