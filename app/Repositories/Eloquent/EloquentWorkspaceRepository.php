<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceUser;
use App\Repositories\Contracts\WorkspaceRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentWorkspaceRepository implements WorkspaceRepositoryInterface
{
    public function create(array $attributes): Workspace
    {
        return Workspace::query()->create($attributes);
    }

    public function update(Workspace $workspace, array $attributes): Workspace
    {
        $workspace->forceFill($attributes)->save();

        return $workspace->refresh();
    }

    public function delete(Workspace $workspace): bool
    {
        return (bool) $workspace->delete();
    }

    public function paginateForUser(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return Workspace::query()
            ->whereHas('memberships', fn ($query) => $query->where('user_id', $user->getKey()))
            ->with(['owner'])
            ->latest()
            ->paginate($perPage);
    }

    public function listForUser(User $user): Collection
    {
        return Workspace::query()
            ->whereHas('memberships', fn ($query) => $query->where('user_id', $user->getKey()))
            ->orderBy('name')
            ->get();
    }

    public function firstForUser(User $user): ?Workspace
    {
        return Workspace::query()
            ->whereHas('memberships', fn ($query) => $query->where('user_id', $user->getKey()))
            ->orderBy('name')
            ->first();
    }

    public function slugExists(string $slug): bool
    {
        return Workspace::query()->where('slug', $slug)->exists();
    }

    public function userBelongsToWorkspace(User $user, Workspace $workspace): bool
    {
        return WorkspaceUser::query()
            ->where('workspace_id', $workspace->getKey())
            ->where('user_id', $user->getKey())
            ->exists();
    }

    public function attachMember(Workspace $workspace, User $user, string $role, ?User $inviter = null): WorkspaceUser
    {
        $membership = WorkspaceUser::query()->withTrashed()->updateOrCreate(
            [
                'workspace_id' => $workspace->getKey(),
                'user_id' => $user->getKey(),
            ],
            [
                'role' => $role,
                'permissions' => [],
                'invited_by' => $inviter?->getKey(),
                'joined_at' => now(),
                'deleted_at' => null,
            ],
        );

        return $membership->refresh();
    }

    public function updateMemberRole(Workspace $workspace, User $user, string $role): ?WorkspaceUser
    {
        $membership = WorkspaceUser::query()
            ->where('workspace_id', $workspace->getKey())
            ->where('user_id', $user->getKey())
            ->first();

        if (! $membership) {
            return null;
        }

        $membership->forceFill(['role' => $role])->save();

        return $membership->refresh();
    }

    public function detachMember(Workspace $workspace, User $user): bool
    {
        $membership = WorkspaceUser::query()
            ->where('workspace_id', $workspace->getKey())
            ->where('user_id', $user->getKey())
            ->first();

        return $membership ? (bool) $membership->delete() : false;
    }
}
