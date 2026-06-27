<?php

namespace Tests\Feature\Social;

use App\Models\FacebookConnection;
use App\Models\FacebookIntegrationSetting;
use App\Models\FacebookLog;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceUser;
use App\Services\Facebook\FacebookConnectorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FacebookIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_channel_page_renders_for_current_workspace(): void
    {
        [$user, $workspace] = $this->workspaceFixture();
        $this->facebookConfig($workspace);

        FacebookConnection::query()->create([
            'workspace_id' => $workspace->getKey(),
            'page_id' => '123',
            'page_name' => 'Wooden Dad Design',
            'facebook_user_name' => 'Aukkaphol',
            'page_category' => 'Shopping & retail',
            'page_access_token' => 'page-token',
            'permissions' => ['pages_manage_posts'],
        ]);

        $this->actingAs($user)
            ->get(route('channels.facebook.index'))
            ->assertOk()
            ->assertSee('Facebook Channel')
            ->assertSee('Facebook App Settings')
            ->assertSee('facebook-app-id')
            ->assertSee('Wooden Dad Design')
            ->assertSee('Connected as Aukkaphol')
            ->assertSee('Test Connection')
            ->assertSee('Sync Page Info');
    }

    public function test_connect_redirects_to_meta_oauth_with_required_permissions(): void
    {
        [$user, $workspace] = $this->workspaceFixture();
        $this->facebookConfig($workspace);

        $response = $this->actingAs($user)->get(route('channels.facebook.connect'));

        $response->assertRedirect();
        $location = $response->headers->get('Location');

        $this->assertStringContainsString('https://www.facebook.com/v23.0/dialog/oauth', $location);
        $this->assertStringContainsString('client_id=facebook-app-id', $location);
        $this->assertStringContainsString('redirect_uri=https%3A%2F%2Fstudio.test%2Fchannels%2Ffacebook%2Fcallback', $location);
        $this->assertStringContainsString('pages_show_list%2Cpages_read_engagement%2Cpages_manage_posts%2Cbusiness_management', $location);
        $this->assertDatabaseHas('facebook_logs', [
            'workspace_id' => $user->current_workspace_id,
            'action' => 'connect_started',
            'status' => FacebookLog::STATUS_STARTED,
        ]);
    }

    public function test_callback_exchanges_code_fetches_pages_and_stores_encrypted_page_token(): void
    {
        [$user, $workspace] = $this->workspaceFixture();
        $this->facebookConfig($workspace);

        Http::fake([
            'graph.facebook.com/v23.0/oauth/access_token*' => Http::sequence()
                ->push(['access_token' => 'short-user-access-token'], 200)
                ->push(['access_token' => 'long-user-access-token', 'expires_in' => 3600], 200),
            'graph.facebook.com/v23.0/me?*' => Http::response([
                'id' => 'user-123',
                'name' => 'Aukkaphol',
                'picture' => ['data' => ['url' => 'https://example.test/user.jpg']],
            ]),
            'graph.facebook.com/v23.0/me/accounts*' => Http::response([
                'data' => [
                    [
                        'id' => 'page-123',
                        'name' => 'Wooden Dad Design',
                        'category' => 'Shopping & retail',
                        'access_token' => 'page-access-token',
                        'fan_count' => 900,
                        'followers_count' => 1200,
                        'verification_status' => 'not_verified',
                        'picture' => ['data' => ['url' => 'https://example.test/page.jpg']],
                        'tasks' => ['CREATE_CONTENT', 'MODERATE'],
                    ],
                ],
            ]),
        ]);

        $response = $this->actingAs($user)
            ->withSession([
                'facebook_oauth_state' => 'state-token',
                'facebook_workspace_id' => $workspace->getKey(),
            ])
            ->get(route('channels.facebook.callback', [
                'state' => 'state-token',
                'code' => 'oauth-code',
            ]));

        $response->assertRedirect(route('channels.facebook.index'));

        $connection = FacebookConnection::query()->firstOrFail();
        $rawToken = DB::table('facebook_connections')->value('page_access_token');

        $this->assertSame($workspace->getKey(), $connection->workspace_id);
        $this->assertSame('user-123', $connection->facebook_user_id);
        $this->assertSame('Aukkaphol', $connection->facebook_user_name);
        $this->assertSame('https://example.test/user.jpg', $connection->facebook_user_avatar);
        $this->assertSame('page-123', $connection->page_id);
        $this->assertSame('page-access-token', $connection->page_access_token);
        $this->assertSame('https://example.test/page.jpg', $connection->page_avatar);
        $this->assertSame(1200, $connection->page_followers_count);
        $this->assertSame(900, $connection->page_likes_count);
        $this->assertSame('not_verified', $connection->page_verification_status);
        $this->assertSame(FacebookConnection::CONNECTION_ACTIVE, $connection->connection_status);
        $this->assertNotNull($connection->last_synced_at);
        $this->assertNotSame('page-access-token', $rawToken);
        $this->assertSame(['CREATE_CONTENT', 'MODERATE'], $connection->permissions);
        $this->assertDatabaseHas('facebook_logs', [
            'facebook_connection_id' => $connection->getKey(),
            'action' => 'connect_success',
            'status' => FacebookLog::STATUS_SUCCESS,
        ]);
    }

    public function test_token_exchange_failure_logs_connect_failed_without_raw_token(): void
    {
        [$user, $workspace] = $this->workspaceFixture();
        $this->facebookConfig($workspace);

        Http::fake([
            'graph.facebook.com/v23.0/oauth/access_token*' => Http::sequence()
                ->push(['access_token' => 'short-user-access-token'], 200)
                ->push(['error' => ['message' => 'Invalid OAuth token']], 400),
        ]);

        $this->actingAs($user)
            ->withSession([
                'facebook_oauth_state' => 'state-token',
                'facebook_workspace_id' => $workspace->getKey(),
            ])
            ->get(route('channels.facebook.callback', [
                'state' => 'state-token',
                'code' => 'oauth-code',
            ]))
            ->assertRedirect(route('channels.facebook.index'))
            ->assertSessionHasErrors('facebook');

        $this->assertDatabaseHas('facebook_logs', [
            'workspace_id' => $workspace->getKey(),
            'action' => 'connect_failed',
            'status' => FacebookLog::STATUS_FAILED,
            'message' => 'Invalid OAuth token',
        ]);
        $this->assertDatabaseMissing('facebook_logs', [
            'message' => 'short-user-access-token',
        ]);
    }

    public function test_service_publishes_text_post_to_connected_page(): void
    {
        [, $workspace] = $this->workspaceFixture();
        $this->facebookConfig($workspace);

        $connection = FacebookConnection::query()->create([
            'workspace_id' => $workspace->getKey(),
            'page_id' => 'page-123',
            'page_name' => 'Wooden Dad Design',
            'page_access_token' => 'page-access-token',
            'permissions' => ['pages_manage_posts'],
        ]);

        Http::fake([
            'graph.facebook.com/v23.0/page-123/feed' => Http::response(['id' => 'page-123_post-456']),
        ]);

        $result = app(FacebookConnectorService::class)->publishTextPost($connection, 'Studio V1 test post');

        $this->assertSame('page-123_post-456', $result['id']);
        Http::assertSent(fn (Request $request): bool => $request->url() === 'https://graph.facebook.com/v23.0/page-123/feed'
            && $request['message'] === 'Studio V1 test post'
            && $request['access_token'] === 'page-access-token');
        $this->assertDatabaseHas('facebook_logs', [
            'facebook_connection_id' => $connection->getKey(),
            'action' => 'publish_success',
            'status' => FacebookLog::STATUS_SUCCESS,
        ]);
    }

    public function test_workspace_facebook_settings_are_saved_encrypted_and_isolated(): void
    {
        [$user, $workspace] = $this->workspaceFixture();
        $otherWorkspace = Workspace::factory()->create();

        $this->actingAs($user)
            ->post(route('channels.facebook.settings'), [
                'app_id' => 'workspace-app-id',
                'app_secret' => 'workspace-secret',
                'redirect_uri' => 'https://workspace.test/channels/facebook/callback',
            ])
            ->assertRedirect();

        $setting = FacebookIntegrationSetting::query()->where('workspace_id', $workspace->getKey())->firstOrFail();
        $rawSecret = DB::table('facebook_integration_settings')->whereKey($setting->getKey())->value('app_secret');

        $this->assertSame('workspace-app-id', $setting->app_id);
        $this->assertSame('workspace-secret', $setting->app_secret);
        $this->assertNotSame('workspace-secret', $rawSecret);
        $this->assertDatabaseMissing('facebook_integration_settings', [
            'workspace_id' => $otherWorkspace->getKey(),
            'app_id' => 'workspace-app-id',
        ]);

        $this->actingAs($user)
            ->get(route('channels.facebook.index'))
            ->assertOk()
            ->assertSee('workspace-app-id')
            ->assertDontSee('workspace-secret');
    }

    public function test_existing_workspace_secret_is_preserved_when_update_leaves_secret_blank(): void
    {
        [$user, $workspace] = $this->workspaceFixture();
        $this->facebookConfig($workspace);

        $this->actingAs($user)
            ->post(route('channels.facebook.settings'), [
                'app_id' => 'updated-app-id',
                'app_secret' => '',
                'redirect_uri' => 'https://updated.test/channels/facebook/callback',
            ])
            ->assertRedirect();

        $setting = FacebookIntegrationSetting::query()->where('workspace_id', $workspace->getKey())->firstOrFail();

        $this->assertSame('updated-app-id', $setting->app_id);
        $this->assertSame('facebook-app-secret', $setting->app_secret);
        $this->assertSame('https://updated.test/channels/facebook/callback', $setting->redirect_uri);
    }

    public function test_test_connection_success_updates_status_and_creates_log(): void
    {
        [$user, $workspace] = $this->workspaceFixture();
        $this->facebookConfig($workspace);
        $connection = $this->connection($workspace, [
            'connection_status' => FacebookConnection::CONNECTION_NEEDS_REFRESH,
            'last_error' => 'Previous error',
        ]);

        Http::fake([
            'graph.facebook.com/v23.0/page-123?*' => Http::response([
                'id' => 'page-123',
                'name' => 'Wooden Dad Design',
            ]),
        ]);

        $this->actingAs($user)
            ->post(route('channels.facebook.test', $connection))
            ->assertRedirect();

        $connection->refresh();
        $this->assertSame(FacebookConnection::CONNECTION_ACTIVE, $connection->connection_status);
        $this->assertNull($connection->last_error);
        $this->assertNotNull($connection->last_tested_at);
        $this->assertDatabaseHas('facebook_logs', [
            'facebook_connection_id' => $connection->getKey(),
            'action' => 'test_success',
            'status' => FacebookLog::STATUS_SUCCESS,
        ]);
    }

    public function test_test_connection_failure_updates_status_error_and_creates_log(): void
    {
        [$user, $workspace] = $this->workspaceFixture();
        $this->facebookConfig($workspace);
        $connection = $this->connection($workspace);

        Http::fake([
            'graph.facebook.com/v23.0/page-123?*' => Http::response(['error' => ['message' => 'Token invalid']], 400),
        ]);

        $this->actingAs($user)
            ->post(route('channels.facebook.test', $connection))
            ->assertRedirect()
            ->assertSessionHasErrors('facebook');

        $connection->refresh();
        $this->assertSame(FacebookConnection::CONNECTION_ERROR, $connection->connection_status);
        $this->assertSame('Token invalid', $connection->last_error);
        $this->assertNotNull($connection->last_tested_at);
        $this->assertDatabaseHas('facebook_logs', [
            'facebook_connection_id' => $connection->getKey(),
            'action' => 'test_failed',
            'status' => FacebookLog::STATUS_FAILED,
        ]);
    }

    public function test_sync_page_info_success_updates_metadata_and_creates_log(): void
    {
        [$user, $workspace] = $this->workspaceFixture();
        $this->facebookConfig($workspace);
        $connection = $this->connection($workspace);

        Http::fake([
            'graph.facebook.com/v23.0/page-123?*' => Http::response([
                'id' => 'page-123',
                'name' => 'Wooden Dad Design',
                'category' => 'Shopping & retail',
                'followers_count' => 1200,
                'fan_count' => 900,
                'verification_status' => 'not_verified',
                'picture' => ['data' => ['url' => 'https://example.test/page.jpg']],
            ]),
        ]);

        $this->actingAs($user)
            ->post(route('channels.facebook.sync', $connection))
            ->assertRedirect();

        $connection->refresh();
        $this->assertSame('Shopping & retail', $connection->page_category);
        $this->assertSame(1200, $connection->page_followers_count);
        $this->assertSame(900, $connection->page_likes_count);
        $this->assertSame('not_verified', $connection->page_verification_status);
        $this->assertSame('https://example.test/page.jpg', $connection->page_avatar);
        $this->assertSame(FacebookConnection::CONNECTION_ACTIVE, $connection->connection_status);
        $this->assertNotNull($connection->last_synced_at);
        $this->assertDatabaseHas('facebook_logs', [
            'facebook_connection_id' => $connection->getKey(),
            'action' => 'sync_success',
            'status' => FacebookLog::STATUS_SUCCESS,
        ]);
    }

    public function test_sync_page_info_failure_updates_status_error_and_creates_log(): void
    {
        [$user, $workspace] = $this->workspaceFixture();
        $this->facebookConfig($workspace);
        $connection = $this->connection($workspace);

        Http::fake([
            'graph.facebook.com/v23.0/page-123?*' => Http::response(['error' => ['message' => 'Permission missing']], 403),
        ]);

        $this->actingAs($user)
            ->post(route('channels.facebook.sync', $connection))
            ->assertRedirect()
            ->assertSessionHasErrors('facebook');

        $connection->refresh();
        $this->assertSame(FacebookConnection::CONNECTION_ERROR, $connection->connection_status);
        $this->assertSame('Permission missing', $connection->last_error);
        $this->assertNotNull($connection->last_synced_at);
        $this->assertDatabaseHas('facebook_logs', [
            'facebook_connection_id' => $connection->getKey(),
            'action' => 'sync_failed',
            'status' => FacebookLog::STATUS_FAILED,
        ]);
    }

    public function test_publish_test_route_success_uses_fixed_message_and_creates_log(): void
    {
        [$user, $workspace] = $this->workspaceFixture();
        $this->facebookConfig($workspace);
        $connection = $this->connection($workspace);

        Http::fake([
            'graph.facebook.com/v23.0/page-123/feed' => Http::response(['id' => 'page-123_post-789']),
        ]);

        $this->actingAs($user)
            ->post(route('channels.facebook.publish-test', $connection))
            ->assertRedirect();

        Http::assertSent(fn (Request $request): bool => $request->url() === 'https://graph.facebook.com/v23.0/page-123/feed'
            && $request['message'] === 'Test post from JARVIS AI Marketing Studio'
            && $request['access_token'] === 'page-access-token');
        $this->assertDatabaseHas('facebook_logs', [
            'facebook_connection_id' => $connection->getKey(),
            'action' => 'publish_success',
            'status' => FacebookLog::STATUS_SUCCESS,
        ]);
    }

    public function test_publish_test_route_failure_updates_status_error_and_creates_log(): void
    {
        [$user, $workspace] = $this->workspaceFixture();
        $this->facebookConfig($workspace);
        $connection = $this->connection($workspace);

        Http::fake([
            'graph.facebook.com/v23.0/page-123/feed' => Http::response(['error' => ['message' => 'Publishing permission missing']], 403),
        ]);

        $this->actingAs($user)
            ->post(route('channels.facebook.publish-test', $connection))
            ->assertRedirect()
            ->assertSessionHasErrors('facebook');

        $connection->refresh();
        $this->assertSame(FacebookConnection::CONNECTION_ERROR, $connection->connection_status);
        $this->assertSame('Publishing permission missing', $connection->last_error);
        $this->assertDatabaseHas('facebook_logs', [
            'facebook_connection_id' => $connection->getKey(),
            'action' => 'publish_failed',
            'status' => FacebookLog::STATUS_FAILED,
            'message' => 'Publishing permission missing',
        ]);
    }

    public function test_tokens_are_encrypted_and_not_exposed_on_channel_page(): void
    {
        [$user, $workspace] = $this->workspaceFixture();
        $connection = $this->connection($workspace, ['page_access_token' => 'super-secret-page-token']);
        $rawToken = DB::table('facebook_connections')->whereKey($connection->getKey())->value('page_access_token');

        $this->assertNotSame('super-secret-page-token', $rawToken);

        $this->actingAs($user)
            ->get(route('channels.facebook.index'))
            ->assertOk()
            ->assertDontSee('super-secret-page-token');
    }

    public function test_channel_actions_are_workspace_scoped(): void
    {
        [$user] = $this->workspaceFixture();
        $otherWorkspace = Workspace::factory()->create();

        $connection = FacebookConnection::query()->create([
            'workspace_id' => $otherWorkspace->getKey(),
            'page_id' => 'outside-page',
            'page_name' => 'Outside Page',
            'page_access_token' => 'page-access-token',
        ]);

        $this->actingAs($user)
            ->post(route('integrations.facebook.publish-test', $connection), [
                'message' => 'Should not publish',
            ])
            ->assertNotFound();

        $this->actingAs($user)
            ->post(route('channels.facebook.publish-test', $connection))
            ->assertNotFound();

        $this->actingAs($user)
            ->post(route('channels.facebook.test', $connection))
            ->assertNotFound();

        $this->actingAs($user)
            ->post(route('channels.facebook.sync', $connection))
            ->assertNotFound();
    }

    private function workspaceFixture(): array
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->getKey()]);

        WorkspaceUser::factory()->create([
            'workspace_id' => $workspace->getKey(),
            'user_id' => $user->getKey(),
            'role' => WorkspaceUser::ROLE_OWNER,
        ]);

        $user->forceFill(['current_workspace_id' => $workspace->getKey()])->save();

        return [$user->refresh(), $workspace->refresh()];
    }

    private function facebookConfig(?Workspace $workspace = null): void
    {
        config([
            'services.facebook.graph_version' => 'v23.0',
        ]);

        if ($workspace) {
            FacebookIntegrationSetting::query()->create([
                'workspace_id' => $workspace->getKey(),
                'app_id' => 'facebook-app-id',
                'app_secret' => 'facebook-app-secret',
                'redirect_uri' => 'https://studio.test/channels/facebook/callback',
            ]);
        }
    }

    private function connection(Workspace $workspace, array $overrides = []): FacebookConnection
    {
        return FacebookConnection::query()->create(array_replace([
            'workspace_id' => $workspace->getKey(),
            'page_id' => 'page-123',
            'page_name' => 'Wooden Dad Design',
            'page_access_token' => 'page-access-token',
            'permissions' => ['pages_manage_posts'],
            'connection_status' => FacebookConnection::CONNECTION_ACTIVE,
        ], $overrides));
    }
}
