<?php

namespace App\Services\Contents;

use App\Models\Brand;
use App\Models\GeneratedContent;
use App\Models\PromptTemplate;
use App\Models\User;
use App\Models\Workspace;
use App\Repositories\Contracts\GeneratedContentRepositoryInterface;
use App\Services\ActivityLogService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GeneratedContentService
{
    public function __construct(
        private readonly GeneratedContentRepositoryInterface $contents,
        private readonly ActivityLogService $activityLog,
    ) {
    }

    public function search(Workspace $workspace, array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->contents->search($workspace, $filters, $perPage);
    }

    public function create(User $actor, Workspace $workspace, array $attributes, Request $request): GeneratedContent
    {
        return DB::transaction(function () use ($actor, $workspace, $attributes, $request): GeneratedContent {
            $brand = Brand::query()->where('workspace_id', $workspace->getKey())->findOrFail($attributes['brand_id']);
            $prompt = PromptTemplate::query()->where('workspace_id', $workspace->getKey())->findOrFail($attributes['prompt_template_id']);
            $variables = $attributes['variables'] ?? [];

            $content = $this->contents->create([
                'workspace_id' => $workspace->getKey(),
                'brand_id' => $brand->getKey(),
                'prompt_template_id' => $prompt->getKey(),
                'created_by' => $actor->getKey(),
                'title' => $attributes['title'],
                'platform' => $attributes['platform'],
                'content_type' => $attributes['content_type'],
                'prompt_snapshot' => $prompt->prompt_template,
                'variables' => $variables,
                'generated_content' => $this->draftContent($brand, $prompt, $attributes, $variables),
                'status' => $attributes['status'],
                'version' => 1,
                'tags' => $this->tags($attributes['tags'] ?? ''),
                'notes' => $attributes['notes'] ?? null,
            ]);

            $this->contents->syncAssets($content, $attributes['asset_ids'] ?? []);
            $this->contents->createVersion($content, $actor);
            $this->activityLog->queue('content.created', 'Generated content draft created.', $content, [], $request, $actor->getKey());

            return $content;
        });
    }

    public function update(User $actor, GeneratedContent $content, array $attributes, Request $request): GeneratedContent
    {
        return DB::transaction(function () use ($actor, $content, $attributes, $request): GeneratedContent {
            $prompt = PromptTemplate::query()->where('workspace_id', $content->workspace_id)->findOrFail($attributes['prompt_template_id']);

            $updated = $this->contents->update($content, [
                'brand_id' => $attributes['brand_id'],
                'prompt_template_id' => $prompt->getKey(),
                'title' => $attributes['title'],
                'platform' => $attributes['platform'],
                'content_type' => $attributes['content_type'],
                'prompt_snapshot' => $prompt->prompt_template,
                'variables' => $attributes['variables'] ?? [],
                'generated_content' => $attributes['generated_content'],
                'status' => GeneratedContent::STATUS_DRAFT,
                'version' => $content->version + 1,
                'tags' => $this->tags($attributes['tags'] ?? ''),
                'notes' => $attributes['notes'] ?? null,
            ]);

            $this->contents->syncAssets($updated, $attributes['asset_ids'] ?? []);
            $this->contents->createVersion($updated, $actor);
            $this->activityLog->queue('content.updated', 'Generated content updated.', $updated, ['version' => $updated->version], $request, $actor->getKey());

            return $updated;
        });
    }

    public function delete(User $actor, GeneratedContent $content, Request $request): void
    {
        DB::transaction(function () use ($actor, $content, $request): void {
            $this->contents->delete($content);
            $this->activityLog->queue('content.deleted', 'Generated content deleted.', $content, [], $request, $actor->getKey());
        });
    }

    public function duplicate(User $actor, GeneratedContent $content, Request $request): GeneratedContent
    {
        return DB::transaction(function () use ($actor, $content, $request): GeneratedContent {
            $duplicate = $this->contents->create([
                'workspace_id' => $content->workspace_id,
                'brand_id' => $content->brand_id,
                'prompt_template_id' => $content->prompt_template_id,
                'created_by' => $actor->getKey(),
                'title' => $content->title.' Copy',
                'platform' => $content->platform,
                'content_type' => $content->content_type,
                'prompt_snapshot' => $content->prompt_snapshot,
                'variables' => $content->variables,
                'generated_content' => $content->generated_content,
                'status' => GeneratedContent::STATUS_DRAFT,
                'version' => 1,
                'tags' => $content->tags,
                'notes' => $content->notes,
            ]);

            $this->contents->syncAssets($duplicate, $content->assets()->pluck('assets.id')->all());
            $this->contents->createVersion($duplicate, $actor);
            $this->activityLog->queue('content.duplicated', 'Generated content duplicated.', $duplicate, ['source_content_id' => $content->getKey()], $request, $actor->getKey());

            return $duplicate;
        });
    }

    public function preview(GeneratedContent $content, array $variables = []): string
    {
        $preview = $content->prompt_snapshot;
        $values = array_replace($content->variables ?? [], $variables);

        foreach ($values as $key => $value) {
            $preview = str_replace(['{{'.$key.'}}', '{{ '.$key.' }}'], (string) $value, $preview);
        }

        return $preview."\n\n--- Provider-Ready Draft ---\n".$content->generated_content;
    }

    private function draftContent(Brand $brand, PromptTemplate $prompt, array $attributes, array $variables): string
    {
        $lines = [
            '[DRAFT CONTENT - AI PROVIDER NOT CONNECTED]',
            'Brand: '.$brand->name,
            'Platform: '.$attributes['platform'],
            'Content Type: '.str_replace('_', ' ', $attributes['content_type']),
            'Prompt: '.$prompt->title,
            'Draft:',
            $this->renderPrompt($prompt->prompt_template, $variables),
        ];

        return implode("\n", $lines);
    }

    private function renderPrompt(string $template, array $variables): string
    {
        foreach ($variables as $key => $value) {
            $template = str_replace(['{{'.$key.'}}', '{{ '.$key.' }}'], (string) $value, $template);
        }

        return $template;
    }

    private function tags(string|array|null $tags): array
    {
        if (is_array($tags)) {
            return array_values(array_unique(array_filter(array_map(fn ($tag) => strtolower(trim((string) $tag)), $tags))));
        }

        return array_values(array_unique(array_filter(array_map(
            fn ($tag) => strtolower(trim($tag)),
            explode(',', (string) $tags),
        ))));
    }
}
