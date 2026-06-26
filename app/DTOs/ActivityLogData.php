<?php

namespace App\DTOs;

use Illuminate\Database\Eloquent\Model;

readonly class ActivityLogData
{
    public function __construct(
        public ?string $userId,
        public ?string $event,
        public ?string $description = null,
        public ?array $properties = [],
        public ?string $ipAddress = null,
        public ?string $userAgent = null,
        public ?string $subjectType = null,
        public ?string $subjectId = null,
    ) {
    }

    public static function forSubject(
        ?string $userId,
        string $event,
        ?string $description,
        ?Model $subject = null,
        array $properties = [],
        ?string $ipAddress = null,
        ?string $userAgent = null,
    ): self {
        return new self(
            userId: $userId,
            event: $event,
            description: $description,
            properties: $properties,
            ipAddress: $ipAddress,
            userAgent: $userAgent,
            subjectType: $subject?->getMorphClass(),
            subjectId: $subject?->getKey(),
        );
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'subject_type' => $this->subjectType,
            'subject_id' => $this->subjectId,
            'event' => $this->event,
            'description' => $this->description,
            'properties' => $this->properties,
            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
        ];
    }
}
