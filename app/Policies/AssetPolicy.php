<?php

namespace App\Policies;

use App\Models\Asset;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceUser;

class AssetPolicy
{
    public function viewAny(User $user, Workspace $workspace): bool
    {
        return $this->isMember($user, $workspace);
    }

    public function create(User $user, Workspace $workspace): bool
    {
        return $this->isMember($user, $workspace);
    }

    public function view(User $user, Asset $asset): bool
    {
        return $this->isMember($user, $asset->workspace);
    }

    public function update(User $user, Asset $asset): bool
    {
        return $asset->uploaded_by === $user->getKey()
            || $this->hasRole($user, $asset->workspace, [WorkspaceUser::ROLE_OWNER, WorkspaceUser::ROLE_ADMIN]);
    }

    public function delete(User $user, Asset $asset): bool
    {
        return $asset->uploaded_by === $user->getKey()
            || $this->hasRole($user, $asset->workspace, [WorkspaceUser::ROLE_OWNER, WorkspaceUser::ROLE_ADMIN]);
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
