<?php

namespace App\Social\Connectors;

use App\Enums\SocialPlatform;
use App\Social\Contracts\LineOaConnectorInterface;

class LineOaConnector extends AbstractOAuthReadyConnector implements LineOaConnectorInterface
{
    public function platform(): SocialPlatform
    {
        return SocialPlatform::LineOa;
    }
}
