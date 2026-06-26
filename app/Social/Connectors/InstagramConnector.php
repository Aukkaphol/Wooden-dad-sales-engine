<?php

namespace App\Social\Connectors;

use App\Enums\SocialPlatform;
use App\Social\Contracts\InstagramConnectorInterface;

class InstagramConnector extends AbstractOAuthReadyConnector implements InstagramConnectorInterface
{
    public function platform(): SocialPlatform
    {
        return SocialPlatform::Instagram;
    }
}
