<?php

namespace App\Repositories\Contracts;

use App\Models\PublishingQueueHistory;
use App\Models\PublishingQueueItem;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PublishingQueueRepositoryInterface
{
    public function create(array $attributes): PublishingQueueItem;

    public function update(PublishingQueueItem $item, array $attributes): PublishingQueueItem;

    public function search(Workspace $workspace, array $filters, int $perPage = 15): LengthAwarePaginator;

    public function createHistory(PublishingQueueItem $item, string $event, ?User $actor, array $attributes = []): PublishingQueueHistory;
}
