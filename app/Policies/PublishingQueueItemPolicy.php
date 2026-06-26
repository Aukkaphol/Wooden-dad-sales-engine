<?php

namespace App\Policies;

use App\Models\PublishingQueueItem;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceUser;

class PublishingQueueItemPolicy
{
    public function viewAny(User $user, Workspace $workspace): bool
    {
        return $this->isMember($user, $workspace);
    }

    public function create(User $user, Workspace $workspace): bool
    {
        return $this->isManager($user, $workspace);
    }

    public function view(User $user, PublishingQueueItem $item): bool
    {
        return $this->isMember($user, $item->workspace);
    }

    public function cancel(User $user, PublishingQueueItem $item): bool
    {
        return $this->isManager($user, $item->workspace)
            && ! in_array($item->status, [PublishingQueueItem::STATUS_CANCELLED, PublishingQueueItem::STATUS_PUBLISHED], true);
    }

    public function retry(User $user, PublishingQueueItem $item): bool
    {
        return $this->isManager($user, $item->workspace)
            && $item->status === PublishingQueueItem::STATUS_FAILED;
    }

    public function manage(User $user, PublishingQueueItem $item): bool
    {
        return $this->isManager($user, $item->workspace);
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
            ])
            ->exists();
    }
}
