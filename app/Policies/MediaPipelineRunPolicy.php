<?php

namespace App\Policies;

use App\Models\MediaPipelineRun;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceUser;

class MediaPipelineRunPolicy
{
    public function viewAny(User $user, Workspace $workspace): bool
    {
        return $this->isMember($user, $workspace);
    }

    public function create(User $user, Workspace $workspace): bool
    {
        return $this->isMember($user, $workspace);
    }

    public function view(User $user, MediaPipelineRun $pipeline): bool
    {
        return $this->isMember($user, $pipeline->workspace);
    }

    public function manage(User $user, MediaPipelineRun $pipeline): bool
    {
        return $this->hasRole($user, $pipeline->workspace, [
            WorkspaceUser::ROLE_OWNER,
            WorkspaceUser::ROLE_ADMIN,
            WorkspaceUser::ROLE_MARKETING_MANAGER,
            WorkspaceUser::ROLE_REVIEWER,
        ]);
    }

    private function isMember(User $user, Workspace $workspace): bool
    {
        return $user->workspaceMemberships()->where('workspace_id', $workspace->getKey())->exists();
    }

    private function hasRole(User $user, Workspace $workspace, array $roles): bool
    {
        return $user->workspaceMemberships()
            ->where('workspace_id', $workspace->getKey())
            ->whereIn('role', $roles)
            ->exists();
    }
}
