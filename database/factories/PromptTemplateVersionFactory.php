<?php

namespace Database\Factories;

use App\Models\PromptTemplate;
use App\Models\PromptTemplateVersion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PromptTemplateVersion>
 */
class PromptTemplateVersionFactory extends Factory
{
    protected $model = PromptTemplateVersion::class;

    public function definition(): array
    {
        return [
            'prompt_template_id' => PromptTemplate::factory(),
            'created_by' => User::factory(),
            'version' => 1,
            'title' => fake()->sentence(3),
            'category' => PromptTemplate::CATEGORY_FACEBOOK_POST,
            'platform' => PromptTemplate::PLATFORM_FACEBOOK,
            'prompt_template' => 'Write a {{tone}} post about {{topic}}.',
            'variables' => ['tone', 'topic'],
            'example_output' => fake()->paragraph(),
            'status' => PromptTemplate::STATUS_DRAFT,
            'tags' => ['social'],
            'recommended_model' => PromptTemplate::MODEL_GPT_55,
        ];
    }
}
