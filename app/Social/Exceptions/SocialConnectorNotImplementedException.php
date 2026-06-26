<?php

namespace App\Social\Exceptions;

use App\Enums\SocialPlatform;
use RuntimeException;

class SocialConnectorNotImplementedException extends RuntimeException
{
    public static function forPlatform(SocialPlatform $platform): self
    {
        return new self($platform->label().' connector is not implemented yet.');
    }
}
