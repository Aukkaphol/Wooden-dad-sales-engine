<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\GeneratedContent;
use App\Models\PromptTemplate;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GeneratedContent>
 */
class GeneratedContentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'brand_id' => Brand::factory(),
            'prompt_template_id' => PromptTemplate::factory(),
            'created_by' => User::factory(),
            'title' => fake()->sentence(4),
            'platform' => 'facebook',
            'content_type' => GeneratedContent::TYPE_FACEBOOK_POST,
            'prompt_snapshot' => 'Write a post about {{topic}}.',
            'variables' => ['topic' => 'launch'],
            'generated_content' => fake()->paragraph(),
            'status' => GeneratedContent::STATUS_DRAFT,
            'scheduled_at' => null,
            'published_at' => null,
            'reviewer_notes' => null,
            'version' => 1,
            'tags' => ['mock'],
            'notes' => null,
        ];
    }
}
