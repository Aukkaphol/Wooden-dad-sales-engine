<?php

namespace Database\Factories;

use App\Models\AiInsight;
use App\Models\AnalyticsRecord;
use App\Models\Brand;
use App\Models\GeneratedContent;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AiInsight>
 */
class AiInsightFactory extends Factory
{
    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'brand_id' => Brand::factory(),
            'generated_content_id' => GeneratedContent::factory(),
            'analytics_record_id' => AnalyticsRecord::factory(),
            'created_by' => User::factory(),
            'insight_type' => AiInsight::TYPE_PERFORMANCE_SUMMARY,
            'title' => fake()->sentence(4),
            'summary' => fake()->paragraph(),
            'recommendation' => fake()->sentence(),
            'priority' => AiInsight::PRIORITY_MEDIUM,
            'status' => AiInsight::STATUS_NEW,
            'metadata' => [],
        ];
    }
}
