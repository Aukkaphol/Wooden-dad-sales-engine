<?php

namespace Database\Factories;

use App\Models\GeneratedContent;
use App\Models\GeneratedContentVersion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GeneratedContentVersion>
 */
class GeneratedContentVersionFactory extends Factory
{
    protected $model = GeneratedContentVersion::class;

    public function definition(): array
    {
        return [
            'generated_content_id' => GeneratedContent::factory(),
            'created_by' => User::factory(),
            'version' => 1,
            'title' => fake()->sentence(4),
            'platform' => 'facebook',
            'content_type' => GeneratedContent::TYPE_FACEBOOK_POST,
            'prompt_snapshot' => 'Write a post about {{topic}}.',
            'variables' => ['topic' => 'launch'],
            'generated_content' => fake()->paragraph(),
            'status' => GeneratedContent::STATUS_DRAFT,
            'tags' => ['mock'],
            'notes' => null,
        ];
    }
}
