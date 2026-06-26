<?php

namespace App\Social\DTOs;

use App\Enums\SocialPlatform;

readonly class OAuthAuthorizationRequest
{
    public function __construct(
        public SocialPlatform $platform,
        public string $authorizationUrl,
        public array $scopes = [],
        public array $parameters = [],
    ) {
    }
}
