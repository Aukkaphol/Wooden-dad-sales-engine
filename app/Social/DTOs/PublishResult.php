<?php

namespace App\Social\DTOs;

readonly class PublishResult
{
    public function __construct(
        public string $providerPostId,
        public array $payload = [],
    ) {
    }
}
