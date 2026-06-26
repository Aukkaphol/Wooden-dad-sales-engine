<?php

namespace App\Repositories\Contracts;

use App\Models\Brand;
use App\Models\PromptTemplate;
use App\Models\PromptTemplateVersion;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PromptTemplateRepositoryInterface
{
    public function create(array $attributes): PromptTemplate;

    public function update(PromptTemplate $prompt, array $attributes): PromptTemplate;

    public function delete(PromptTemplate $prompt): bool;

    public function search(Workspace $workspace, array $filters, int $perPage = 15): LengthAwarePaginator;

    public function slugExists(Workspace $workspace, Brand $brand, string $slug): bool;

    public function createVersion(PromptTemplate $prompt, ?User $actor = null): PromptTemplateVersion;
}
