<?php

namespace App\Repositories\Eloquent;

use App\Models\GeneratedContent;
use App\Models\GeneratedContentVersion;
use App\Models\User;
use App\Models\Workspace;
use App\Repositories\Contracts\GeneratedContentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EloquentGeneratedContentRepository implements GeneratedContentRepositoryInterface
{
    public function create(array $attributes): GeneratedContent
    {
        return GeneratedContent::query()->create($attributes);
    }

    public function update(GeneratedContent $content, array $attributes): GeneratedContent
    {
        $content->forceFill($attributes)->save();

        return $content->refresh();
    }

    public function delete(GeneratedContent $content): bool
    {
        return (bool) $content->delete();
    }

    public function search(Workspace $workspace, array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return GeneratedContent::query()
            ->with(['brand', 'promptTemplate', 'creator'])
            ->where('workspace_id', $workspace->getKey())
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('generated_content', 'like', "%{$search}%")
                        ->orWhere('notes', 'like', "%{$search}%");
                });
            })
            ->when($filters['brand_id'] ?? null, fn ($query, string $brandId) => $query->where('brand_id', $brandId))
            ->when($filters['prompt_template_id'] ?? null, fn ($query, string $promptId) => $query->where('prompt_template_id', $promptId))
            ->when($filters['platform'] ?? null, fn ($query, string $platform) => $query->where('platform', $platform))
            ->when($filters['content_type'] ?? null, fn ($query, string $type) => $query->where('content_type', $type))
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->when($filters['tag'] ?? null, fn ($query, string $tag) => $query->whereJsonContains('tags', strtolower($tag)))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function syncAssets(GeneratedContent $content, array $assetIds): void
    {
        $assetIds = array_values(array_unique(array_filter($assetIds)));

        DB::table('generated_content_assets')
            ->where('generated_content_id', $content->getKey())
            ->delete();

        if ($assetIds === []) {
            return;
        }

        DB::table('generated_content_assets')->insert(array_map(fn (string $assetId): array => [
            'id' => (string) Str::uuid(),
            'generated_content_id' => $content->getKey(),
            'asset_id' => $assetId,
            'created_at' => now(),
            'updated_at' => now(),
        ], $assetIds));
    }

    public function createVersion(GeneratedContent $content, ?User $actor = null): GeneratedContentVersion
    {
        return GeneratedContentVersion::query()->create([
            'generated_content_id' => $content->getKey(),
            'created_by' => $actor?->getKey(),
            'version' => $content->version,
            'title' => $content->title,
            'platform' => $content->platform,
            'content_type' => $content->content_type,
            'prompt_snapshot' => $content->prompt_snapshot,
            'variables' => $content->variables,
            'generated_content' => $content->generated_content,
            'status' => $content->status,
            'tags' => $content->tags,
            'notes' => $content->notes,
        ]);
    }
}
