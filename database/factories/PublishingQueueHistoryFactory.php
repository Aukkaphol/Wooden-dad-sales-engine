<?php

namespace Database\Factories;

use App\Models\PublishingQueueHistory;
use App\Models\PublishingQueueItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PublishingQueueHistory>
 */
class PublishingQueueHistoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'publishing_queue_item_id' => PublishingQueueItem::factory(),
            'actor_id' => User::factory(),
            'event' => PublishingQueueHistory::EVENT_SCHEDULED,
            'previous_status' => PublishingQueueItem::STATUS_WAITING,
            'new_status' => PublishingQueueItem::STATUS_SCHEDULED,
            'comment' => fake()->sentence(),
            'metadata' => [],
        ];
    }
}
