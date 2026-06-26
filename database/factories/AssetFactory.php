<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\Brand;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Asset>
 */
class AssetFactory extends Factory
{
    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'brand_id' => Brand::factory(),
            'uploaded_by' => User::factory(),
            'name' => fake()->words(3, true),
            'type' => Asset::TYPE_IMAGE,
            'mime_type' => 'image/png',
            'disk' => 'local',
            'path' => 'workspaces/example/brands/example/assets/example/file.png',
            'thumbnail_path' => null,
            'extension' => 'png',
            'size_bytes' => 1024,
            'width' => 1200,
            'height' => 800,
            'duration_seconds' => null,
            'metadata' => [],
            'tags' => ['marketing'],
            'category' => 'social',
            'status' => Asset::STATUS_DRAFT,
        ];
    }
}
