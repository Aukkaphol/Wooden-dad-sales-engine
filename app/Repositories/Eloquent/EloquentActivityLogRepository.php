<?php

namespace App\Repositories\Eloquent;

use App\Models\ActivityLog;
use App\Repositories\Contracts\ActivityLogRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentActivityLogRepository implements ActivityLogRepositoryInterface
{
    public function create(array $attributes): ActivityLog
    {
        return ActivityLog::query()->create($attributes);
    }

    public function paginateForUser(string $userId, int $perPage = 15): LengthAwarePaginator
    {
        return ActivityLog::query()
            ->where('user_id', $userId)
            ->latest()
            ->paginate($perPage);
    }
}
