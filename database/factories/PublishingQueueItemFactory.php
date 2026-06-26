<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\GeneratedContent;
use App\Models\PublishingQueueItem;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PublishingQueueItem>
 */
class PublishingQueueItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'brand_id' => Brand::factory(),
            'generated_content_id' => GeneratedContent::factory(),
            'created_by' => User::factory(),
            'platform' => 'facebook',
            'status' => PublishingQueueItem::STATUS_WAITING,
            'scheduled_at' => null,
            'published_at' => null,
            'retry_count' => 0,
            'failure_reason' => null,
            'priority' => 100,
        ];
    }
}
