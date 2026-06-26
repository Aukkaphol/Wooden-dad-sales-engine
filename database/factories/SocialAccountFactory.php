<?php

namespace Database\Factories;

use App\Enums\SocialPlatform;
use App\Models\Brand;
use App\Models\SocialAccount;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SocialAccount>
 */
class SocialAccountFactory extends Factory
{
    public function definition(): array
    {
        $workspace = Workspace::factory();

        return [
            'workspace_id' => $workspace,
            'brand_id' => Brand::factory(['workspace_id' => $workspace]),
            'connected_by' => User::factory(),
            'platform' => fake()->randomElement(SocialPlatform::cases()),
            'provider_account_id' => fake()->uuid(),
            'name' => fake()->company(),
            'username' => fake()->userName(),
            'status' => SocialAccount::STATUS_DRAFT,
            'scopes' => [],
            'oauth_payload' => [],
            'metadata' => [],
        ];
    }
}
