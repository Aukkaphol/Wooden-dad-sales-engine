<?php

namespace Database\Factories;

use App\Models\ContentApprovalHistory;
use App\Models\GeneratedContent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContentApprovalHistory>
 */
class ContentApprovalHistoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'generated_content_id' => GeneratedContent::factory(),
            'reviewer_id' => User::factory(),
            'decision' => ContentApprovalHistory::DECISION_SUBMITTED,
            'comment' => fake()->sentence(),
            'previous_status' => GeneratedContent::STATUS_DRAFT,
            'new_status' => GeneratedContent::STATUS_IN_REVIEW,
            'decided_at' => now(),
        ];
    }
}
