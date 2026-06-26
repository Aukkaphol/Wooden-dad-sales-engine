<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Brand>
 */
class BrandFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->company();

        return [
            'workspace_id' => Workspace::factory(),
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(6)),
            'primary_color' => '#0f172a',
            'secondary_color' => '#06b6d4',
            'font_family' => 'Inter',
            'tone' => fake()->randomElement(['professional', 'friendly', 'premium', 'bold']),
            'voice' => fake()->paragraph(),
            'default_prompt' => fake()->paragraph(),
            'default_cta' => 'Contact us today',
            'contact_information' => [
                'email' => fake()->companyEmail(),
                'phone' => fake()->phoneNumber(),
                'website' => fake()->url(),
                'address' => fake()->address(),
            ],
            'social_links' => [
                'facebook' => fake()->url(),
                'instagram' => fake()->url(),
                'linkedin' => fake()->url(),
                'tiktok' => fake()->url(),
            ],
            'status' => 'active',
        ];
    }
}
