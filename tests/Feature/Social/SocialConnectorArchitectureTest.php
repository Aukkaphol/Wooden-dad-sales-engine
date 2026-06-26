<?php

namespace Tests\Feature\Social;

use App\Enums\SocialPlatform;
use App\Models\Brand;
use App\Models\GeneratedContent;
use App\Models\PromptTemplate;
use App\Models\PublishingQueueItem;
use App\Models\SocialAccount;
use App\Models\User;
use App\Models\Workspace;
use App\Social\Contracts\FacebookConnectorInterface;
use App\Social\Contracts\InstagramConnectorInterface;
use App\Social\Contracts\LineOaConnectorInterface;
use App\Social\Contracts\SocialConnectorRegistryInterface;
use App\Social\Contracts\TikTokConnectorInterface;
use App\Social\Exceptions\SocialConnectorNotImplementedException;
use App\Services\Publishing\SocialPublishingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SocialConnectorArchitectureTest extends TestCase
{
    use RefreshDatabase;

    public function test_social_account_uses_uuid_and_platform_enum(): void
    {
        $account = SocialAccount::factory()->create([
            'platform' => SocialPlatform::Facebook,
            'oauth_payload' => ['access_token' => 'secret-token'],
        ]);

        $this->assertIsString($account->id);
        $this->assertSame(36, strlen($account->id));
        $this->assertSame(SocialPlatform::Facebook, $account->refresh()->platform);
        $this->assertSame('secret-token', $account->oauth_payload['access_token']);
    }

    public function test_connector_interfaces_resolve_from_container(): void
    {
        $this->assertSame(SocialPlatform::Facebook, app(FacebookConnectorInterface::class)->platform());
        $this->assertSame(SocialPlatform::Instagram, app(InstagramConnectorInterface::class)->platform());
        $this->assertSame(SocialPlatform::TikTok, app(TikTokConnectorInterface::class)->platform());
        $this->assertSame(SocialPlatform::LineOa, app(LineOaConnectorInterface::class)->platform());
    }

    public function test_registry_resolves_connector_by_platform(): void
    {
        $registry = app(SocialConnectorRegistryInterface::class);

        $this->assertContains('facebook', $registry->platforms());
        $this->assertSame(SocialPlatform::Instagram, $registry->connectorFor(SocialPlatform::Instagram)->platform());
    }

    public function test_provider_independent_publishing_refuses_unimplemented_connector(): void
    {
        [$workspace, $brand, $user, $item] = $this->publishingFixture();
        $account = SocialAccount::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $brand->getKey(),
            'connected_by' => $user->getKey(),
            'platform' => SocialPlatform::Facebook,
        ]);

        $this->expectException(SocialConnectorNotImplementedException::class);

        app(SocialPublishingService::class)->publish($item, $account);
    }

    public function test_provider_independent_publishing_validates_workspace_scope(): void
    {
        [, , , $item] = $this->publishingFixture();
        $otherWorkspace = Workspace::factory()->create();
        $account = SocialAccount::factory()->create([
            'workspace_id' => $otherWorkspace->getKey(),
            'platform' => SocialPlatform::Instagram,
        ]);

        $this->expectException(ValidationException::class);

        app(SocialPublishingService::class)->publish($item, $account);
    }

    private function publishingFixture(): array
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->getKey()]);
        $brand = Brand::factory()->create(['workspace_id' => $workspace->getKey()]);
        $prompt = PromptTemplate::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $brand->getKey(),
            'created_by' => $user->getKey(),
        ]);
        $content = GeneratedContent::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $brand->getKey(),
            'prompt_template_id' => $prompt->getKey(),
            'created_by' => $user->getKey(),
        ]);
        $item = PublishingQueueItem::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'brand_id' => $brand->getKey(),
            'generated_content_id' => $content->getKey(),
            'created_by' => $user->getKey(),
        ]);

        return [$workspace->refresh(), $brand->refresh(), $user->refresh(), $item->refresh()];
    }
}
