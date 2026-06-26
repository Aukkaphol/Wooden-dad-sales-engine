<?php

namespace App\Social\Contracts;

use App\Enums\SocialPlatform;

interface SocialConnectorRegistryInterface
{
    public function connectorFor(SocialPlatform $platform): SocialConnectorInterface;

    public function platforms(): array;
}
