<?php

namespace App\Services\Facebook;

use App\Models\FacebookConnection;
use App\Models\FacebookIntegrationSetting;
use App\Models\FacebookLog;
use App\Models\Workspace;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class FacebookConnectorService
{
    public const PERMISSIONS = [
        'pages_show_list',
        'pages_read_engagement',
        'pages_manage_posts',
        'business_management',
    ];

    public function authorizationUrl(Workspace $workspace, string $state): string
    {
        $settings = $this->settingsFor($workspace);
        $this->log($workspace, null, 'connect_started', FacebookLog::STATUS_STARTED, 'Facebook OAuth connection started.');

        return 'https://www.facebook.com/'.$this->graphVersion().'/dialog/oauth?'.http_build_query([
            'client_id' => $settings->app_id,
            'redirect_uri' => $settings->redirect_uri,
            'state' => $state,
            'scope' => implode(',', self::PERMISSIONS),
            'response_type' => 'code',
        ]);
    }

    /**
     * @return Collection<int, FacebookConnection>
     *
     * @throws RequestException
     */
    public function storePagesFromCallback(Workspace $workspace, string $code): Collection
    {
        try {
            $settings = $this->settingsFor($workspace);
            $shortLivedTokenPayload = $this->exchangeCodeForUserToken($settings, $code);
            $longLivedTokenPayload = $this->exchangeForLongLivedUserToken($settings, $shortLivedTokenPayload['access_token']);
            $userAccessToken = $longLivedTokenPayload['access_token'];
            $userProfile = $this->fetchUserProfile($userAccessToken);
            $pages = $this->fetchManagedPages($userAccessToken);
            $expiresAt = isset($longLivedTokenPayload['expires_in'])
                ? now()->addSeconds((int) $longLivedTokenPayload['expires_in'])
                : null;

            return collect($pages)->map(function (array $page) use ($workspace, $expiresAt, $userProfile): FacebookConnection {
                $connection = FacebookConnection::query()->updateOrCreate(
                    [
                        'workspace_id' => $workspace->getKey(),
                        'page_id' => (string) $page['id'],
                    ],
                    [
                        'facebook_user_id' => $userProfile['id'] ?? null,
                        'facebook_user_name' => $userProfile['name'] ?? null,
                        'facebook_user_avatar' => data_get($userProfile, 'picture.data.url'),
                        'page_name' => $page['name'],
                        'page_access_token' => $page['access_token'],
                        'page_avatar' => data_get($page, 'picture.data.url'),
                        'page_category' => $page['category'] ?? null,
                        'page_followers_count' => $page['followers_count'] ?? null,
                        'page_likes_count' => $page['fan_count'] ?? null,
                        'page_verification_status' => $page['verification_status'] ?? null,
                        'token_expires_at' => $expiresAt,
                        'permissions' => $page['tasks'] ?? null,
                        'status' => FacebookConnection::STATUS_ACTIVE,
                        'connection_status' => FacebookConnection::CONNECTION_ACTIVE,
                        'last_synced_at' => now(),
                        'last_error' => null,
                    ],
                );

                $this->log($workspace, $connection, 'connect_success', FacebookLog::STATUS_SUCCESS, 'Facebook Page connected.', [
                    'page_id' => $connection->page_id,
                    'page_name' => $connection->page_name,
                ]);

                return $connection;
            });
        } catch (RequestException $exception) {
            $this->log($workspace, null, 'connect_failed', FacebookLog::STATUS_FAILED, $this->failureMessage($exception));

            throw $exception;
        }
    }

    /**
     * @return array<string, mixed>
     *
     * @throws RequestException
     */
    public function publishTextPost(FacebookConnection $connection, string $message): array
    {
        Validator::make(['message' => $message], [
            'message' => ['required', 'string', 'max:63206'],
        ])->validate();

        if ($connection->connection_status !== FacebookConnection::CONNECTION_ACTIVE) {
            throw ValidationException::withMessages([
                'page' => 'The selected Facebook Page connection is not active.',
            ]);
        }

        try {
            $response = Http::asForm()
                ->post($this->graphUrl('/'.$connection->page_id.'/feed'), [
                    'message' => $message,
                    'access_token' => $connection->page_access_token,
                ])
                ->throw()
                ->json();

            $this->log($connection->workspace, $connection, 'publish_success', FacebookLog::STATUS_SUCCESS, 'Facebook test post published.', [
                'post_id' => $response['id'] ?? null,
            ]);

            return $response;
        } catch (RequestException $exception) {
            $connection->forceFill([
                'connection_status' => FacebookConnection::CONNECTION_ERROR,
                'last_error' => $this->failureMessage($exception),
            ])->save();

            $this->log($connection->workspace, $connection, 'publish_failed', FacebookLog::STATUS_FAILED, $this->failureMessage($exception));

            throw $exception;
        }
    }

    /**
     * @return array<string, mixed>
     *
     * @throws RequestException
     */
    public function testConnection(FacebookConnection $connection): array
    {
        try {
            $response = Http::get($this->graphUrl('/'.$connection->page_id), [
                'fields' => 'id,name',
                'access_token' => $connection->page_access_token,
            ])->throw()->json();

            $connection->forceFill([
                'page_name' => $response['name'] ?? $connection->page_name,
                'connection_status' => FacebookConnection::CONNECTION_ACTIVE,
                'last_tested_at' => now(),
                'last_error' => null,
            ])->save();

            $this->log($connection->workspace, $connection, 'test_success', FacebookLog::STATUS_SUCCESS, 'Facebook Page connection tested.', [
                'page_id' => $response['id'] ?? $connection->page_id,
            ]);

            return $response;
        } catch (RequestException $exception) {
            $connection->forceFill([
                'connection_status' => FacebookConnection::CONNECTION_ERROR,
                'last_tested_at' => now(),
                'last_error' => $this->failureMessage($exception),
            ])->save();

            $this->log($connection->workspace, $connection, 'test_failed', FacebookLog::STATUS_FAILED, $this->failureMessage($exception));

            throw $exception;
        }
    }

    /**
     * @return array<string, mixed>
     *
     * @throws RequestException
     */
    public function syncPageInfo(FacebookConnection $connection): array
    {
        try {
            $response = Http::get($this->graphUrl('/'.$connection->page_id), [
                'fields' => 'id,name,category,followers_count,fan_count,verification_status,picture.type(large){url}',
                'access_token' => $connection->page_access_token,
            ])->throw()->json();

            $connection->forceFill([
                'page_name' => $response['name'] ?? $connection->page_name,
                'page_avatar' => data_get($response, 'picture.data.url'),
                'page_category' => $response['category'] ?? null,
                'page_followers_count' => $response['followers_count'] ?? null,
                'page_likes_count' => $response['fan_count'] ?? null,
                'page_verification_status' => $response['verification_status'] ?? null,
                'connection_status' => FacebookConnection::CONNECTION_ACTIVE,
                'last_synced_at' => now(),
                'last_error' => null,
            ])->save();

            $this->log($connection->workspace, $connection, 'sync_success', FacebookLog::STATUS_SUCCESS, 'Facebook Page info synced.', [
                'page_id' => $connection->page_id,
            ]);

            return $response;
        } catch (RequestException $exception) {
            $connection->forceFill([
                'connection_status' => FacebookConnection::CONNECTION_ERROR,
                'last_synced_at' => now(),
                'last_error' => $this->failureMessage($exception),
            ])->save();

            $this->log($connection->workspace, $connection, 'sync_failed', FacebookLog::STATUS_FAILED, $this->failureMessage($exception));

            throw $exception;
        }
    }

    /**
     * @return array<string, mixed>
     *
     * @throws RequestException
     */
    private function exchangeCodeForUserToken(FacebookIntegrationSetting $settings, string $code): array
    {
        return Http::get($this->graphUrl('/oauth/access_token'), [
            'client_id' => $settings->app_id,
            'client_secret' => $settings->app_secret,
            'redirect_uri' => $settings->redirect_uri,
            'code' => $code,
        ])->throw()->json();
    }

    /**
     * @return array<string, mixed>
     *
     * @throws RequestException
     */
    private function exchangeForLongLivedUserToken(FacebookIntegrationSetting $settings, string $shortLivedUserAccessToken): array
    {
        return Http::get($this->graphUrl('/oauth/access_token'), [
            'grant_type' => 'fb_exchange_token',
            'client_id' => $settings->app_id,
            'client_secret' => $settings->app_secret,
            'fb_exchange_token' => $shortLivedUserAccessToken,
        ])->throw()->json();
    }

    /**
     * @return array<int, array<string, mixed>>
     *
     * @throws RequestException
     */
    private function fetchManagedPages(string $userAccessToken): array
    {
        $response = Http::get($this->graphUrl('/me/accounts'), [
            'fields' => 'id,name,access_token,category,fan_count,followers_count,picture,verification_status,tasks',
            'access_token' => $userAccessToken,
        ])->throw()->json();

        return array_values(array_filter($response['data'] ?? [], fn (array $page): bool => isset(
            $page['id'],
            $page['name'],
            $page['access_token'],
        )));
    }

    /**
     * @return array<string, mixed>
     *
     * @throws RequestException
     */
    private function fetchUserProfile(string $userAccessToken): array
    {
        return Http::get($this->graphUrl('/me'), [
            'fields' => 'id,name,picture.type(large){url}',
            'access_token' => $userAccessToken,
        ])->throw()->json();
    }

    private function log(
        Workspace $workspace,
        ?FacebookConnection $connection,
        string $action,
        string $status,
        ?string $message = null,
        ?array $payload = null,
    ): FacebookLog {
        return FacebookLog::query()->create([
            'workspace_id' => $workspace->getKey(),
            'facebook_connection_id' => $connection?->getKey(),
            'action' => $action,
            'status' => $status,
            'message' => $message,
            'payload' => $payload,
        ]);
    }

    private function failureMessage(RequestException $exception): string
    {
        $message = data_get($exception->response?->json(), 'error.message');

        if (is_string($message) && $message !== '') {
            return $message;
        }

        return 'Facebook Graph API request failed with status '.$exception->response?->status().'.';
    }

    private function settingsFor(Workspace $workspace): FacebookIntegrationSetting
    {
        $settings = FacebookIntegrationSetting::query()
            ->where('workspace_id', $workspace->getKey())
            ->first();

        if (! $settings) {
            throw ValidationException::withMessages([
                'facebook_settings' => 'Save Facebook App settings before connecting a Page.',
            ]);
        }

        Validator::make($settings->only(['app_id', 'app_secret', 'redirect_uri']), [
            'app_id' => ['required', 'string'],
            'app_secret' => ['required', 'string'],
            'redirect_uri' => ['required', 'url'],
        ])->validate();

        return $settings;
    }

    private function graphUrl(string $path): string
    {
        return 'https://graph.facebook.com/'.$this->graphVersion().'/'.ltrim($path, '/');
    }

    private function graphVersion(): string
    {
        return Str::of((string) config('services.facebook.graph_version', 'v23.0'))
            ->trim('/')
            ->toString();
    }
}
