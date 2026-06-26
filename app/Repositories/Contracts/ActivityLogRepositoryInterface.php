<?php

namespace App\Repositories\Contracts;

use App\Models\ActivityLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ActivityLogRepositoryInterface
{
    public function create(array $attributes): ActivityLog;

    public function paginateForUser(string $userId, int $perPage = 15): LengthAwarePaginator;
}
