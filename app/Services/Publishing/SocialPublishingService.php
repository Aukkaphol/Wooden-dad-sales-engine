<?php

namespace App\Services\Publishing;

use App\Enums\SocialPlatform;
use App\Models\PublishingQueueItem;
use App\Models\SocialAccount;
use App\Social\Contracts\SocialConnectorRegistryInterface;
use App\Social\DTOs\PublishPayload;
use App\Social\DTOs\PublishResult;
use Illuminate\Validation\ValidationException;

class SocialPublishingService
{
    public function __construct(private readonly SocialConnectorRegistryInterface $connectors)
    {
    }

    public function publish(PublishingQueueItem $item, SocialAccount $account): PublishResult
    {
        if ($account->workspace_id !== $item->workspace_id) {
            throw ValidationException::withMessages([
                'social_account_id' => 'The social account must belong to the same workspace as the queue item.',
            ]);
        }

        $platform = $account->platform instanceof SocialPlatform
            ? $account->platform
            : SocialPlatform::from((string) $account->platform);

        $connector = $this->connectors->connectorFor($platform);

        return $connector->publish(new PublishPayload(
            account: $account,
            content: $item->generatedContent,
            queueItem: $item,
            assets: $item->generatedContent->assets()->get()->all(),
            metadata: ['provider_independent' => true],
        ));
    }
}
