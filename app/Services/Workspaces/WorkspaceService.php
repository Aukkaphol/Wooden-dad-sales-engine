<?php

namespace App\Services\Workspaces;

use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceUser;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\WorkspaceRepositoryInterface;
use App\Services\ActivityLogService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class WorkspaceService
{
    public function __construct(
        private readonly WorkspaceRepositoryInterface $workspaces,
        private readonly UserRepositoryInterface $users,
        private readonly ActivityLogService $activityLog,
    ) {
    }

    public function listForUser(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return $this->workspaces->paginateForUser($user, $perPage);
    }

    public function create(User $owner, array $attributes, Request $request): Workspace
    {
        return DB::transaction(function () use ($owner, $attributes, $request): Workspace {
            $workspace = $this->workspaces->create([
                'owner_id' => $owner->getKey(),
                'name' => $attributes['name'],
                'slug' => $this->uniqueSlug($attributes['name']),
                'industry' => $attributes['industry'] ?? null,
                'timezone' => $attributes['timezone'] ?? $owner->timezone ?? 'UTC',
                'default_language' => $attributes['default_language'] ?? $owner->locale ?? 'en',
                'status' => 'active',
            ]);

            $this->workspaces->attachMember($workspace, $owner, WorkspaceUser::ROLE_OWNER, $owner);
            $this->users->update($owner, ['current_workspace_id' => $workspace->getKey()]);

            $this->activityLog->queue(
                event: 'workspace.created',
                description: 'Workspace created.',
                subject: $workspace,
                request: $request,
                userId: $owner->getKey(),
            );

            return $workspace;
        });
    }

    public function update(User $actor, Workspace $workspace, array $attributes, Request $request): Workspace
    {
        return DB::transaction(function () use ($actor, $workspace, $attributes, $request): Workspace {
            $updated = $this->workspaces->update($workspace, [
                'name' => $attributes['name'],
                'industry' => $attributes['industry'] ?? null,
                'timezone' => $attributes['timezone'] ?? 'UTC',
                'default_language' => $attributes['default_language'] ?? 'en',
                'status' => $attributes['status'],
            ]);

            $this->activityLog->queue(
                event: 'workspace.updated',
                description: 'Workspace updated.',
                subject: $updated,
                request: $request,
                userId: $actor->getKey(),
            );

            return $updated;
        });
    }

    public function delete(User $actor, Workspace $workspace, Request $request): void
    {
        DB::transaction(function () use ($actor, $workspace, $request): void {
            $this->workspaces->delete($workspace);

            if ($actor->current_workspace_id === $workspace->getKey()) {
                $nextWorkspace = $this->workspaces->firstForUser($actor);
                $this->users->update($actor, ['current_workspace_id' => $nextWorkspace?->getKey()]);
            }

            $this->activityLog->queue(
                event: 'workspace.deleted',
                description: 'Workspace deleted.',
                subject: $workspace,
                request: $request,
                userId: $actor->getKey(),
            );
        });
    }

    public function switch(User $actor, Workspace $workspace, Request $request): User
    {
        if (! $this->workspaces->userBelongsToWorkspace($actor, $workspace)) {
            throw ValidationException::withMessages([
                'workspace' => 'You do not have access to this workspace.',
            ]);
        }

        $updated = $this->users->update($actor, ['current_workspace_id' => $workspace->getKey()]);

        $this->activityLog->queue(
            event: 'workspace.switched',
            description: 'Current workspace switched.',
            subject: $workspace,
            request: $request,
            userId: $actor->getKey(),
        );

        return $updated;
    }

    public function addMember(User $actor, Workspace $workspace, string $email, string $role, Request $request): WorkspaceUser
    {
        return DB::transaction(function () use ($actor, $workspace, $email, $role, $request): WorkspaceUser {
            $user = $this->users->findByEmail($email);

            if (! $user) {
                throw ValidationException::withMessages(['email' => 'User not found.']);
            }

            if ($workspace->isOwnedBy($user)) {
                throw ValidationException::withMessages(['email' => 'The workspace owner is already a member.']);
            }

            $membership = $this->workspaces->attachMember($workspace, $user, $role, $actor);

            $this->activityLog->queue(
                event: 'workspace.member_added',
                description: 'Workspace member added.',
                subject: $workspace,
                properties: ['member_id' => $user->getKey(), 'role' => $role],
                request: $request,
                userId: $actor->getKey(),
            );

            return $membership;
        });
    }

    public function updateMemberRole(User $actor, Workspace $workspace, User $member, string $role, Request $request): WorkspaceUser
    {
        if ($workspace->isOwnedBy($member)) {
            throw ValidationException::withMessages(['role' => 'The workspace owner role cannot be changed.']);
        }

        $membership = $this->workspaces->updateMemberRole($workspace, $member, $role);

        if (! $membership) {
            throw ValidationException::withMessages(['member' => 'Member not found in this workspace.']);
        }

        $this->activityLog->queue(
            event: 'workspace.member_role_updated',
            description: 'Workspace member role updated.',
            subject: $workspace,
            properties: ['member_id' => $member->getKey(), 'role' => $role],
            request: $request,
            userId: $actor->getKey(),
        );

        return $membership;
    }

    public function removeMember(User $actor, Workspace $workspace, User $member, Request $request): void
    {
        if ($workspace->isOwnedBy($member)) {
            throw ValidationException::withMessages(['member' => 'The workspace owner cannot be removed.']);
        }

        DB::transaction(function () use ($actor, $workspace, $member, $request): void {
            $this->workspaces->detachMember($workspace, $member);

            if ($member->current_workspace_id === $workspace->getKey()) {
                $nextWorkspace = $this->workspaces->firstForUser($member);
                $this->users->update($member, ['current_workspace_id' => $nextWorkspace?->getKey()]);
            }

            $this->activityLog->queue(
                event: 'workspace.member_removed',
                description: 'Workspace member removed.',
                subject: $workspace,
                properties: ['member_id' => $member->getKey()],
                request: $request,
                userId: $actor->getKey(),
            );
        });
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'workspace';
        $slug = $base;
        $suffix = 2;

        while ($this->workspaces->slugExists($slug)) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
