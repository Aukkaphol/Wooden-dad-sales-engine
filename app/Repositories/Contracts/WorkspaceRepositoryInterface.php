<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceUser;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface WorkspaceRepositoryInterface
{
    public function create(array $attributes): Workspace;

    public function update(Workspace $workspace, array $attributes): Workspace;

    public function delete(Workspace $workspace): bool;

    public function paginateForUser(User $user, int $perPage = 15): LengthAwarePaginator;

    public function listForUser(User $user): Collection;

    public function firstForUser(User $user): ?Workspace;

    public function slugExists(string $slug): bool;

    public function userBelongsToWorkspace(User $user, Workspace $workspace): bool;

    public function attachMember(Workspace $workspace, User $user, string $role, ?User $inviter = null): WorkspaceUser;

    public function updateMemberRole(Workspace $workspace, User $user, string $role): ?WorkspaceUser;

    public function detachMember(Workspace $workspace, User $user): bool;
}
