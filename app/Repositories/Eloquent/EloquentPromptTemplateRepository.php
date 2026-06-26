<?php

namespace App\Repositories\Eloquent;

use App\Models\Brand;
use App\Models\PromptTemplate;
use App\Models\PromptTemplateVersion;
use App\Models\User;
use App\Models\Workspace;
use App\Repositories\Contracts\PromptTemplateRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentPromptTemplateRepository implements PromptTemplateRepositoryInterface
{
    public function create(array $attributes): PromptTemplate
    {
        return PromptTemplate::query()->create($attributes);
    }

    public function update(PromptTemplate $prompt, array $attributes): PromptTemplate
    {
        $prompt->forceFill($attributes)->save();

        return $prompt->refresh();
    }

    public function delete(PromptTemplate $prompt): bool
    {
        return (bool) $prompt->delete();
    }

    public function search(Workspace $workspace, array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return PromptTemplate::query()
            ->with(['brand', 'creator'])
            ->where('workspace_id', $workspace->getKey())
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('prompt_template', 'like', "%{$search}%")
                        ->orWhere('example_output', 'like', "%{$search}%");
                });
            })
            ->when($filters['brand_id'] ?? null, fn ($query, string $brandId) => $query->where('brand_id', $brandId))
            ->when($filters['category'] ?? null, fn ($query, string $category) => $query->where('category', $category))
            ->when($filters['platform'] ?? null, fn ($query, string $platform) => $query->where('platform', $platform))
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->when($filters['model'] ?? null, fn ($query, string $model) => $query->where('recommended_model', $model))
            ->when(($filters['favorite'] ?? null) !== null && ($filters['favorite'] ?? '') !== '', fn ($query) => $query->where('favorite', true))
            ->when($filters['tag'] ?? null, fn ($query, string $tag) => $query->whereJsonContains('tags', strtolower($tag)))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function slugExists(Workspace $workspace, Brand $brand, string $slug): bool
    {
        return PromptTemplate::query()
            ->where('workspace_id', $workspace->getKey())
            ->where('brand_id', $brand->getKey())
            ->where('slug', $slug)
            ->exists();
    }

    public function createVersion(PromptTemplate $prompt, ?User $actor = null): PromptTemplateVersion
    {
        return PromptTemplateVersion::query()->create([
            'prompt_template_id' => $prompt->getKey(),
            'created_by' => $actor?->getKey(),
            'version' => $prompt->version,
            'title' => $prompt->title,
            'category' => $prompt->category,
            'platform' => $prompt->platform,
            'prompt_template' => $prompt->prompt_template,
            'variables' => $prompt->variables,
            'example_output' => $prompt->example_output,
            'status' => $prompt->status,
            'tags' => $prompt->tags,
            'recommended_model' => $prompt->recommended_model,
        ]);
    }
}
