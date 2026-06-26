<?php

namespace App\Repositories\Contracts;

use App\Models\AiInsight;
use App\Models\Workspace;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AiInsightRepositoryInterface
{
    public function create(array $attributes): AiInsight;

    public function update(AiInsight $insight, array $attributes): AiInsight;

    public function delete(AiInsight $insight): bool;

    public function search(Workspace $workspace, array $filters, int $perPage = 15): LengthAwarePaginator;
}
