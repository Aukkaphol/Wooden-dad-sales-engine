<?php

namespace App\Policies;

use App\Models\GeneratedContent;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceUser;

class GeneratedContentPolicy
{
    public function viewAny(User $user, Workspace $workspace): bool
    {
        return $this->isMember($user, $workspace);
    }

    public function create(User $user, Workspace $workspace): bool
    {
        return $this->isMember($user, $workspace);
    }

    public function view(User $user, GeneratedContent $content): bool
    {
        return $this->isMember($user, $content->workspace);
    }

    public function update(User $user, GeneratedContent $content): bool
    {
        if ($content->isPublished()) {
            return false;
        }

        if ($content->created_by === $user->getKey()) {
            return $content->status === GeneratedContent::STATUS_DRAFT;
        }

        return $this->hasRole($user, $content->workspace, [WorkspaceUser::ROLE_OWNER, WorkspaceUser::ROLE_ADMIN]);
    }

    public function delete(User $user, GeneratedContent $content): bool
    {
        if ($content->isPublished()) {
            return false;
        }

        return $content->created_by === $user->getKey()
            || $this->hasRole($user, $content->workspace, [WorkspaceUser::ROLE_OWNER, WorkspaceUser::ROLE_ADMIN]);
    }

    public function duplicate(User $user, GeneratedContent $content): bool
    {
        return $this->isMember($user, $content->workspace);
    }

    public function submitForReview(User $user, GeneratedContent $content): bool
    {
        return ! $content->isPublished()
            && in_array($content->status, [GeneratedContent::STATUS_DRAFT, GeneratedContent::STATUS_REJECTED], true)
            && ($content->created_by === $user->getKey() || $this->isManager($user, $content->workspace));
    }

    public function approve(User $user, GeneratedContent $content): bool
    {
        return ! $content->isPublished() && $this->isReviewer($user, $content->workspace);
    }

    public function reject(User $user, GeneratedContent $content): bool
    {
        return ! $content->isPublished() && $this->isReviewer($user, $content->workspace);
    }

    public function returnWithComment(User $user, GeneratedContent $content): bool
    {
        return ! $content->isPublished() && $this->isReviewer($user, $content->workspace);
    }

    public function schedule(User $user, GeneratedContent $content): bool
    {
        return ! $content->isPublished() && $this->isManager($user, $content->workspace);
    }

    public function publish(User $user, GeneratedContent $content): bool
    {
        return ! $content->isPublished() && $this->isManager($user, $content->workspace);
    }

    public function archive(User $user, GeneratedContent $content): bool
    {
        return $this->isManager($user, $content->workspace);
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

    private function isReviewer(User $user, Workspace $workspace): bool
    {
        return $this->hasRole($user, $workspace, [
            WorkspaceUser::ROLE_OWNER,
            WorkspaceUser::ROLE_ADMIN,
            WorkspaceUser::ROLE_REVIEWER,
        ]);
    }

    private function isManager(User $user, Workspace $workspace): bool
    {
        return $this->hasRole($user, $workspace, [
            WorkspaceUser::ROLE_OWNER,
            WorkspaceUser::ROLE_ADMIN,
            WorkspaceUser::ROLE_MARKETING_MANAGER,
        ]);
    }
}
