<?php

namespace App\Social\DTOs;

use App\Models\GeneratedContent;
use App\Models\PublishingQueueItem;
use App\Models\SocialAccount;

readonly class PublishPayload
{
    public function __construct(
        public SocialAccount $account,
        public GeneratedContent $content,
        public PublishingQueueItem $queueItem,
        public array $assets = [],
        public array $metadata = [],
    ) {
    }
}
