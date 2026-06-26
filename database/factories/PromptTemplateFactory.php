<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\PromptTemplate;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PromptTemplate>
 */
class PromptTemplateFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->sentence(3);

        return [
            'workspace_id' => Workspace::factory(),
            'brand_id' => Brand::factory(),
            'created_by' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title).'-'.Str::lower(Str::random(6)),
            'category' => PromptTemplate::CATEGORY_FACEBOOK_POST,
            'platform' => PromptTemplate::PLATFORM_FACEBOOK,
            'prompt_template' => 'Write a {{tone}} post for {{brand_name}} about {{topic}}.',
            'variables' => ['tone', 'brand_name', 'topic'],
            'example_output' => fake()->paragraph(),
            'version' => 1,
            'status' => PromptTemplate::STATUS_DRAFT,
            'tags' => ['social'],
            'favorite' => false,
            'usage_count' => 0,
            'success_rate' => 0,
            'rating_average' => 0,
            'rating_count' => 0,
            'recommended_model' => PromptTemplate::MODEL_GPT_55,
            'last_used_at' => null,
        ];
    }
}
