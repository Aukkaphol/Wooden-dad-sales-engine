<?php

namespace App\Social\Connectors;

use App\Enums\SocialPlatform;
use App\Models\SocialAccount;
use App\Social\Contracts\SocialConnectorInterface;
use App\Social\DTOs\OAuthAuthorizationRequest;
use App\Social\DTOs\OAuthCallbackData;
use App\Social\DTOs\PublishPayload;
use App\Social\DTOs\PublishResult;
use App\Social\Exceptions\SocialConnectorNotImplementedException;

abstract class AbstractOAuthReadyConnector implements SocialConnectorInterface
{
    public function supportsOAuth(): bool
    {
        return true;
    }

    public function authorizationRequest(array $state = []): OAuthAuthorizationRequest
    {
        throw SocialConnectorNotImplementedException::forPlatform($this->platform());
    }

    public function connect(OAuthCallbackData $callback): SocialAccount
    {
        throw SocialConnectorNotImplementedException::forPlatform($this->platform());
    }

    public function publish(PublishPayload $payload): PublishResult
    {
        throw SocialConnectorNotImplementedException::forPlatform($this->platform());
    }

    abstract public function platform(): SocialPlatform;
}
