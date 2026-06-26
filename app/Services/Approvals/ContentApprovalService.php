<?php

namespace App\Services\Approvals;

use App\Models\ContentApprovalHistory;
use App\Models\GeneratedContent;
use App\Models\User;
use App\Repositories\Contracts\ContentApprovalRepositoryInterface;
use App\Repositories\Contracts\GeneratedContentRepositoryInterface;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ContentApprovalService
{
    public function __construct(
        private readonly GeneratedContentRepositoryInterface $contents,
        private readonly ContentApprovalRepositoryInterface $approvals,
        private readonly ActivityLogService $activityLog,
    ) {
    }

    public function submitForReview(User $actor, GeneratedContent $content, array $attributes, Request $request): GeneratedContent
    {
        $this->ensureStatus($content, [GeneratedContent::STATUS_DRAFT, GeneratedContent::STATUS_REJECTED]);

        return $this->transition($actor, $content, ContentApprovalHistory::DECISION_SUBMITTED, GeneratedContent::STATUS_IN_REVIEW, $attributes, $request);
    }

    public function approve(User $actor, GeneratedContent $content, array $attributes, Request $request): GeneratedContent
    {
        $this->ensureStatus($content, [GeneratedContent::STATUS_IN_REVIEW, GeneratedContent::STATUS_REJECTED]);

        return $this->transition($actor, $content, ContentApprovalHistory::DECISION_APPROVED, GeneratedContent::STATUS_APPROVED, $attributes, $request);
    }

    public function reject(User $actor, GeneratedContent $content, array $attributes, Request $request): GeneratedContent
    {
        $this->ensureStatus($content, [GeneratedContent::STATUS_IN_REVIEW, GeneratedContent::STATUS_APPROVED]);

        return $this->transition($actor, $content, ContentApprovalHistory::DECISION_REJECTED, GeneratedContent::STATUS_REJECTED, $attributes, $request);
    }

    public function returnWithComment(User $actor, GeneratedContent $content, array $attributes, Request $request): GeneratedContent
    {
        $this->ensureStatus($content, [GeneratedContent::STATUS_IN_REVIEW, GeneratedContent::STATUS_APPROVED, GeneratedContent::STATUS_REJECTED]);

        return $this->transition($actor, $content, ContentApprovalHistory::DECISION_RETURNED, GeneratedContent::STATUS_DRAFT, $attributes, $request);
    }

    public function schedule(User $actor, GeneratedContent $content, array $attributes, Request $request): GeneratedContent
    {
        $this->ensureStatus($content, [GeneratedContent::STATUS_APPROVED]);

        return $this->transition(
            $actor,
            $content,
            ContentApprovalHistory::DECISION_SCHEDULED,
            GeneratedContent::STATUS_SCHEDULED,
            $attributes + ['scheduled_at' => $attributes['scheduled_at']],
            $request,
        );
    }

    public function markPublished(User $actor, GeneratedContent $content, array $attributes, Request $request): GeneratedContent
    {
        $this->ensureStatus($content, [GeneratedContent::STATUS_APPROVED, GeneratedContent::STATUS_SCHEDULED]);

        return $this->transition(
            $actor,
            $content,
            ContentApprovalHistory::DECISION_PUBLISHED,
            GeneratedContent::STATUS_PUBLISHED,
            $attributes + ['published_at' => now()],
            $request,
        );
    }

    public function archive(User $actor, GeneratedContent $content, array $attributes, Request $request): GeneratedContent
    {
        return $this->transition($actor, $content, ContentApprovalHistory::DECISION_ARCHIVED, GeneratedContent::STATUS_ARCHIVED, $attributes, $request);
    }

    private function transition(
        User $actor,
        GeneratedContent $content,
        string $decision,
        string $newStatus,
        array $attributes,
        Request $request,
    ): GeneratedContent {
        return DB::transaction(function () use ($actor, $content, $decision, $newStatus, $attributes, $request): GeneratedContent {
            $previousStatus = $content->status;

            $updated = $this->contents->update($content, array_filter([
                'status' => $newStatus,
                'scheduled_at' => $attributes['scheduled_at'] ?? ($newStatus === GeneratedContent::STATUS_SCHEDULED ? $content->scheduled_at : null),
                'published_at' => $attributes['published_at'] ?? $content->published_at,
                'reviewer_notes' => $attributes['reviewer_notes'] ?? $content->reviewer_notes,
            ], fn ($value) => $value !== null));

            $this->approvals->createHistory($updated, [
                'reviewer_id' => $actor->getKey(),
                'decision' => $decision,
                'comment' => $attributes['comment'] ?? null,
                'previous_status' => $previousStatus,
                'new_status' => $newStatus,
                'decided_at' => now(),
            ]);

            $this->activityLog->queue(
                event: 'content.workflow_'.$decision,
                description: 'Content workflow transition recorded.',
                subject: $updated,
                properties: ['previous_status' => $previousStatus, 'new_status' => $newStatus],
                request: $request,
                userId: $actor->getKey(),
            );

            return $updated;
        });
    }

    private function ensureStatus(GeneratedContent $content, array $allowedStatuses): void
    {
        if (! in_array($content->status, $allowedStatuses, true)) {
            throw ValidationException::withMessages([
                'status' => 'This workflow action is not allowed for the current content status.',
            ]);
        }
    }
}
