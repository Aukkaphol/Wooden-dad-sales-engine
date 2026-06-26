<?php

namespace Database\Factories;

use App\Models\AnalyticsRecord;
use App\Models\Brand;
use App\Models\GeneratedContent;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AnalyticsRecord>
 */
class AnalyticsRecordFactory extends Factory
{
    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'brand_id' => Brand::factory(),
            'generated_content_id' => GeneratedContent::factory(),
            'publishing_queue_item_id' => null,
            'created_by' => User::factory(),
            'platform' => 'facebook',
            'posted_at' => now()->subDay(),
            'captured_at' => now(),
            'views' => 1000,
            'reach' => 700,
            'impressions' => 1300,
            'likes' => 30,
            'comments' => 5,
            'shares' => 4,
            'saves' => 3,
            'follows_gained' => 2,
            'link_clicks' => 12,
            'ctr' => 1.2,
            'engagement_rate' => 4.2,
            'estimated_revenue' => 0,
            'cost' => 0,
            'roas' => 0,
            'notes' => null,
            'audience_breakdown' => [],
            'metadata' => [],
            'score' => 50,
            'score_reason' => 'Factory score.',
            'recommendation' => 'Factory recommendation.',
        ];
    }
}
