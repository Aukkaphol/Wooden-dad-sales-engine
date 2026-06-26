<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\PublishingJob;
use App\Models\PublishingQueueItem;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PublishingJob>
 */
class PublishingJobFactory extends Factory
{
    public function definition(): array
    {
        $workspace = Workspace::factory();

        return [
            'workspace_id' => $workspace,
            'brand_id' => Brand::factory(['workspace_id' => $workspace]),
            'publishing_queue_item_id' => PublishingQueueItem::factory(),
            'created_by' => User::factory(),
            'platform' => 'facebook',
            'status' => PublishingJob::STATUS_DRAFT,
            'attempts' => 0,
            'metadata' => [],
        ];
    }
}
