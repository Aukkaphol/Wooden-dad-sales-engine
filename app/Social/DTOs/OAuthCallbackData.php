<?php

namespace App\Social\DTOs;

readonly class OAuthCallbackData
{
    public function __construct(
        public string $code,
        public ?string $state = null,
        public array $payload = [],
    ) {
    }
}
