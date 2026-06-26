<?php

namespace App\Social\Connectors;

use App\Enums\SocialPlatform;
use App\Social\Contracts\TikTokConnectorInterface;

class TikTokConnector extends AbstractOAuthReadyConnector implements TikTokConnectorInterface
{
    public function platform(): SocialPlatform
    {
        return SocialPlatform::TikTok;
    }
}
