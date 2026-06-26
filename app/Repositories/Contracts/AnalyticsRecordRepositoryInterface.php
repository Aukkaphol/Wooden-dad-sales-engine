<?php

namespace App\Repositories\Contracts;

use App\Models\AnalyticsRecord;
use App\Models\Workspace;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AnalyticsRecordRepositoryInterface
{
    public function create(array $attributes): AnalyticsRecord;

    public function update(AnalyticsRecord $record, array $attributes): AnalyticsRecord;

    public function delete(AnalyticsRecord $record): bool;

    public function search(Workspace $workspace, array $filters, int $perPage = 15): LengthAwarePaginator;
}
