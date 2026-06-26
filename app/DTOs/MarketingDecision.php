<?php

namespace App\DTOs;

readonly class MarketingDecision
{
    public function __construct(
        public string $type,
        public string $title,
        public string $recommendation,
        public int $confidence,
        public string $reasoning,
        public array $metadata = [],
    ) {
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'title' => $this->title,
            'recommendation' => $this->recommendation,
            'confidence' => $this->confidence,
            'reasoning' => $this->reasoning,
            'metadata' => $this->metadata,
        ];
    }
}
