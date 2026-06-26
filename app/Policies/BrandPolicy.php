<?php

namespace App\Policies;

use App\Models\Brand;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceUser;

class BrandPolicy
{
    public function viewAny(User $user, Workspace $workspace): bool
    {
        return $this->isMember($user, $workspace);
    }

    public function create(User $user, Workspace $workspace): bool
    {
        return $this->hasRole($user, $workspace, [WorkspaceUser::ROLE_OWNER, WorkspaceUser::ROLE_ADMIN]);
    }

    public function view(User $user, Brand $brand): bool
    {
        return $this->isMember($user, $brand->workspace);
    }

    public function update(User $user, Brand $brand): bool
    {
        return $this->hasRole($user, $brand->workspace, [WorkspaceUser::ROLE_OWNER, WorkspaceUser::ROLE_ADMIN]);
    }

    public function delete(User $user, Brand $brand): bool
    {
        return $this->hasRole($user, $brand->workspace, [WorkspaceUser::ROLE_OWNER, WorkspaceUser::ROLE_ADMIN]);
    }

    private function isMember(User $user, Workspace $workspace): bool
    {
        return $user->workspaceMemberships()
            ->where('workspace_id', $workspace->getKey())
            ->exists();
    }

    private function hasRole(User $user, Workspace $workspace, array $roles): bool
    {
        return $user->workspaceMemberships()
            ->where('workspace_id', $workspace->getKey())
            ->whereIn('role', $roles)
            ->exists();
    }
}
