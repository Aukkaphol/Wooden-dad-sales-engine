<?php

namespace App\Repositories\Contracts;

use App\Models\PublishingJob;
use App\Models\PublishingLog;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PublishingJobRepositoryInterface
{
    public function create(array $attributes): PublishingJob;

    public function update(PublishingJob $job, array $attributes): PublishingJob;

    public function find(string $id): ?PublishingJob;

    public function search(array $filters, int $perPage = 15): LengthAwarePaginator;

    public function log(PublishingJob $job, string $event, string $message, string $level = 'info', ?User $actor = null, array $context = []): PublishingLog;
}
