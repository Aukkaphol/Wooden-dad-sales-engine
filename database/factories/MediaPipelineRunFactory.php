<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\MediaPipelineRun;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MediaPipelineRun>
 */
class MediaPipelineRunFactory extends Factory
{
    public function definition(): array
    {
        $workspace = Workspace::factory();

        return [
            'workspace_id' => $workspace,
            'brand_id' => Brand::factory(['workspace_id' => $workspace]),
            'created_by' => User::factory(),
            'asset_ids' => [],
            'prompt_version' => 1,
            'current_stage' => MediaPipelineRun::STAGE_ASSETS,
            'status' => MediaPipelineRun::STATUS_DRAFT,
            'metadata' => [],
        ];
    }
}
