<?php

namespace App\Repositories\Contracts;

use App\Models\MediaPipelineHistory;
use App\Models\MediaPipelineRun;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

interface MediaPipelineRepositoryInterface
{
    public function create(array $attributes): MediaPipelineRun;

    public function update(MediaPipelineRun $pipeline, array $attributes): MediaPipelineRun;

    public function search(Workspace $workspace, array $filters, int $perPage = 10): LengthAwarePaginator;

    public function record(MediaPipelineRun $pipeline, string $stage, string $event, string $description, ?User $actor = null, ?Model $subject = null, array $metadata = []): MediaPipelineHistory;
}
