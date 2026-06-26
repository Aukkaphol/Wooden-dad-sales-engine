<?php

namespace App\Services;

use App\DTOs\ActivityLogData;
use App\Jobs\LogActivityJob;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ActivityLogService
{
    public function queue(
        string $event,
        ?string $description = null,
        ?Model $subject = null,
        array $properties = [],
        ?Request $request = null,
        ?string $userId = null,
    ): void {
        $resolvedUserId = $userId ?? $request?->user()?->getKey();

        LogActivityJob::dispatch(ActivityLogData::forSubject(
            userId: $resolvedUserId,
            event: $event,
            description: $description,
            subject: $subject,
            properties: $properties,
            ipAddress: $request?->ip(),
            userAgent: $request?->userAgent(),
        ));
    }
}
