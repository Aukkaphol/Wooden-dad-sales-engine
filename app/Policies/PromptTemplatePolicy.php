<?php

namespace App\Policies;

use App\Models\PromptTemplate;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceUser;

class PromptTemplatePolicy
{
    public function viewAny(User $user, Workspace $workspace): bool
    {
        return $this->isMember($user, $workspace);
    }

    public function create(User $user, Workspace $workspace): bool
    {
        return $this->hasRole($user, $workspace, [WorkspaceUser::ROLE_OWNER, WorkspaceUser::ROLE_ADMIN, WorkspaceUser::ROLE_MEMBER]);
    }

    public function view(User $user, PromptTemplate $prompt): bool
    {
        return $this->isMember($user, $prompt->workspace);
    }

    public function update(User $user, PromptTemplate $prompt): bool
    {
        return $prompt->created_by === $user->getKey()
            || $this->hasRole($user, $prompt->workspace, [WorkspaceUser::ROLE_OWNER, WorkspaceUser::ROLE_ADMIN]);
    }

    public function delete(User $user, PromptTemplate $prompt): bool
    {
        return $prompt->created_by === $user->getKey()
            || $this->hasRole($user, $prompt->workspace, [WorkspaceUser::ROLE_OWNER, WorkspaceUser::ROLE_ADMIN]);
    }

    public function duplicate(User $user, PromptTemplate $prompt): bool
    {
        return $this->isMember($user, $prompt->workspace);
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
