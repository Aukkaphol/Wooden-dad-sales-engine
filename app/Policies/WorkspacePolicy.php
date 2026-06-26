<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceUser;

class WorkspacePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function view(User $user, Workspace $workspace): bool
    {
        return $this->isMember($user, $workspace);
    }

    public function update(User $user, Workspace $workspace): bool
    {
        return $this->hasRole($user, $workspace, [WorkspaceUser::ROLE_OWNER, WorkspaceUser::ROLE_ADMIN]);
    }

    public function delete(User $user, Workspace $workspace): bool
    {
        return $workspace->isOwnedBy($user);
    }

    public function switch(User $user, Workspace $workspace): bool
    {
        return $this->isMember($user, $workspace);
    }

    public function manageMembers(User $user, Workspace $workspace): bool
    {
        return $this->hasRole($user, $workspace, [WorkspaceUser::ROLE_OWNER, WorkspaceUser::ROLE_ADMIN]);
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
