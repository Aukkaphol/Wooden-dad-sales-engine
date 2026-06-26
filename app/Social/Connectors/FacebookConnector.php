<?php

namespace App\Social\Connectors;

use App\Enums\SocialPlatform;
use App\Social\Contracts\FacebookConnectorInterface;

class FacebookConnector extends AbstractOAuthReadyConnector implements FacebookConnectorInterface
{
    public function platform(): SocialPlatform
    {
        return SocialPlatform::Facebook;
    }
}
