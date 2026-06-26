<?php

namespace App\Policies;

use App\Models\AnalyticsRecord;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceUser;

class AnalyticsRecordPolicy
{
    public function viewAny(User $user, Workspace $workspace): bool
    {
        return $this->isMember($user, $workspace);
    }

    public function create(User $user, Workspace $workspace): bool
    {
        return $this->isManager($user, $workspace);
    }

    public function view(User $user, AnalyticsRecord $record): bool
    {
        return $this->isMember($user, $record->workspace);
    }

    public function update(User $user, AnalyticsRecord $record): bool
    {
        return $this->isManager($user, $record->workspace);
    }

    public function delete(User $user, AnalyticsRecord $record): bool
    {
        return $this->isManager($user, $record->workspace);
    }

    private function isMember(User $user, Workspace $workspace): bool
    {
        return $user->workspaceMemberships()->where('workspace_id', $workspace->getKey())->exists();
    }

    private function isManager(User $user, Workspace $workspace): bool
    {
        return $user->workspaceMemberships()
            ->where('workspace_id', $workspace->getKey())
            ->whereIn('role', [
                WorkspaceUser::ROLE_OWNER,
                WorkspaceUser::ROLE_ADMIN,
                WorkspaceUser::ROLE_MARKETING_MANAGER,
                WorkspaceUser::ROLE_REVIEWER,
            ])
            ->exists();
    }
}
