<?php

namespace App\Social;

use App\Enums\SocialPlatform;
use App\Social\Contracts\SocialConnectorInterface;
use App\Social\Contracts\SocialConnectorRegistryInterface;
use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;

class SocialConnectorRegistry implements SocialConnectorRegistryInterface
{
    /**
     * @param array<string, class-string<SocialConnectorInterface>> $connectors
     */
    public function __construct(
        private readonly Container $container,
        private readonly array $connectors,
    ) {
    }

    public function connectorFor(SocialPlatform $platform): SocialConnectorInterface
    {
        $connector = $this->connectors[$platform->value] ?? null;

        if ($connector === null) {
            throw new InvalidArgumentException('No connector registered for '.$platform->label().'.');
        }

        return $this->container->make($connector);
    }

    public function platforms(): array
    {
        return array_keys($this->connectors);
    }
}
