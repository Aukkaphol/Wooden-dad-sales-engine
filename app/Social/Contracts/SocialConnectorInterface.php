<?php

namespace App\Social\Contracts;

use App\Enums\SocialPlatform;
use App\Models\SocialAccount;
use App\Social\DTOs\OAuthAuthorizationRequest;
use App\Social\DTOs\OAuthCallbackData;
use App\Social\DTOs\PublishPayload;
use App\Social\DTOs\PublishResult;

interface SocialConnectorInterface
{
    public function platform(): SocialPlatform;

    public function supportsOAuth(): bool;

    public function authorizationRequest(array $state = []): OAuthAuthorizationRequest;

    public function connect(OAuthCallbackData $callback): SocialAccount;

    public function publish(PublishPayload $payload): PublishResult;
}
