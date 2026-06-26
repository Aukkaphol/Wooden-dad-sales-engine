<?php

namespace App\Services\Prompts;

use App\Models\Brand;
use App\Models\PromptTemplate;
use App\Models\User;
use App\Models\Workspace;
use App\Repositories\Contracts\PromptTemplateRepositoryInterface;
use App\Services\ActivityLogService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PromptTemplateService
{
    public function __construct(
        private readonly PromptTemplateRepositoryInterface $prompts,
        private readonly ActivityLogService $activityLog,
    ) {
    }

    public function search(Workspace $workspace, array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->prompts->search($workspace, $filters, $perPage);
    }

    public function create(User $actor, Workspace $workspace, Brand $brand, array $attributes, Request $request): PromptTemplate
    {
        return DB::transaction(function () use ($actor, $workspace, $brand, $attributes, $request): PromptTemplate {
            $payload = $this->payload($attributes) + [
                'workspace_id' => $workspace->getKey(),
                'brand_id' => $brand->getKey(),
                'created_by' => $actor->getKey(),
                'slug' => $this->uniqueSlug($workspace, $brand, $attributes['title']),
                'version' => 1,
                'usage_count' => 0,
                'success_rate' => 0,
                'rating_average' => 0,
                'rating_count' => 0,
            ];

            $this->validateVariables($payload['prompt_template'], $payload['variables']);

            $prompt = $this->prompts->create($payload);
            $this->prompts->createVersion($prompt, $actor);

            $this->activityLog->queue('prompt.created', 'Prompt template created.', $prompt, [], $request, $actor->getKey());

            return $prompt;
        });
    }

    public function update(User $actor, PromptTemplate $prompt, Brand $brand, array $attributes, Request $request): PromptTemplate
    {
        return DB::transaction(function () use ($actor, $prompt, $brand, $attributes, $request): PromptTemplate {
            $payload = $this->payload($attributes) + [
                'brand_id' => $brand->getKey(),
                'version' => $prompt->version + 1,
            ];

            $this->validateVariables($payload['prompt_template'], $payload['variables']);

            $updated = $this->prompts->update($prompt, $payload);
            $this->prompts->createVersion($updated, $actor);

            $this->activityLog->queue('prompt.updated', 'Prompt template updated.', $updated, ['version' => $updated->version], $request, $actor->getKey());

            return $updated;
        });
    }

    public function delete(User $actor, PromptTemplate $prompt, Request $request): void
    {
        DB::transaction(function () use ($actor, $prompt, $request): void {
            $this->prompts->delete($prompt);
            $this->activityLog->queue('prompt.deleted', 'Prompt template deleted.', $prompt, [], $request, $actor->getKey());
        });
    }

    public function duplicate(User $actor, PromptTemplate $prompt, Request $request): PromptTemplate
    {
        return DB::transaction(function () use ($actor, $prompt, $request): PromptTemplate {
            $copyTitle = $prompt->title.' Copy';
            $duplicate = $this->prompts->create([
                'workspace_id' => $prompt->workspace_id,
                'brand_id' => $prompt->brand_id,
                'created_by' => $actor->getKey(),
                'title' => $copyTitle,
                'slug' => $this->uniqueSlug($prompt->workspace, $prompt->brand, $copyTitle),
                'category' => $prompt->category,
                'platform' => $prompt->platform,
                'prompt_template' => $prompt->prompt_template,
                'variables' => $prompt->variables,
                'example_output' => $prompt->example_output,
                'version' => 1,
                'status' => PromptTemplate::STATUS_DRAFT,
                'tags' => $prompt->tags,
                'favorite' => false,
                'usage_count' => 0,
                'success_rate' => 0,
                'rating_average' => 0,
                'rating_count' => 0,
                'recommended_model' => $prompt->recommended_model,
            ]);

            $this->prompts->createVersion($duplicate, $actor);
            $this->activityLog->queue('prompt.duplicated', 'Prompt template duplicated.', $duplicate, ['source_prompt_id' => $prompt->getKey()], $request, $actor->getKey());

            return $duplicate;
        });
    }

    public function toggleFavorite(User $actor, PromptTemplate $prompt, Request $request): PromptTemplate
    {
        $updated = $this->prompts->update($prompt, ['favorite' => ! $prompt->favorite]);
        $this->activityLog->queue('prompt.favorite_toggled', 'Prompt favorite toggled.', $updated, ['favorite' => $updated->favorite], $request, $actor->getKey());

        return $updated;
    }

    public function preview(PromptTemplate $prompt, array $values): array
    {
        $missing = [];
        $preview = $prompt->prompt_template;

        foreach (($prompt->variables ?? []) as $variable) {
            $value = $values[$variable] ?? null;

            if (blank($value)) {
                $missing[] = $variable;
                continue;
            }

            $preview = str_replace(['{{'.$variable.'}}', '{{ '.$variable.' }}'], $value, $preview);
        }

        return [
            'preview' => $preview,
            'missing' => $missing,
        ];
    }

    public function rate(User $actor, PromptTemplate $prompt, int $rating, bool $successful, Request $request): PromptTemplate
    {
        $newCount = $prompt->rating_count + 1;
        $newAverage = (($prompt->rating_average * $prompt->rating_count) + $rating) / $newCount;
        $usageCount = $prompt->usage_count + 1;
        $successes = (int) round(($prompt->success_rate / 100) * $prompt->usage_count);
        $newSuccessRate = (($successes + (int) $successful) / $usageCount) * 100;

        $updated = $this->prompts->update($prompt, [
            'rating_average' => round($newAverage, 2),
            'rating_count' => $newCount,
            'usage_count' => $usageCount,
            'success_rate' => round($newSuccessRate, 2),
            'last_used_at' => now(),
        ]);

        $this->activityLog->queue('prompt.rated', 'Prompt template rated.', $updated, ['rating' => $rating, 'successful' => $successful], $request, $actor->getKey());

        return $updated;
    }

    public function markUsed(User $actor, PromptTemplate $prompt, Request $request): PromptTemplate
    {
        $updated = $this->prompts->update($prompt, [
            'usage_count' => $prompt->usage_count + 1,
            'last_used_at' => now(),
        ]);

        $this->activityLog->queue('prompt.used', 'Prompt template usage recorded.', $updated, [], $request, $actor->getKey());

        return $updated;
    }

    private function payload(array $attributes): array
    {
        return [
            'title' => $attributes['title'],
            'category' => $attributes['category'],
            'platform' => $attributes['platform'],
            'prompt_template' => $attributes['prompt_template'],
            'variables' => $this->variables($attributes['variables'] ?? ''),
            'example_output' => $attributes['example_output'] ?? null,
            'status' => $attributes['status'],
            'tags' => $this->tags($attributes['tags'] ?? ''),
            'favorite' => (bool) ($attributes['favorite'] ?? false),
            'recommended_model' => $attributes['recommended_model'] ?? null,
        ];
    }

    private function variables(string|array|null $variables): array
    {
        if (is_array($variables)) {
            return array_values(array_unique(array_filter(array_map(fn ($variable) => Str::snake(trim((string) $variable)), $variables))));
        }

        return array_values(array_unique(array_filter(array_map(
            fn ($variable) => Str::snake(trim($variable)),
            explode(',', (string) $variables),
        ))));
    }

    private function tags(string|array|null $tags): array
    {
        if (is_array($tags)) {
            return array_values(array_unique(array_filter(array_map(fn ($tag) => Str::lower(trim((string) $tag)), $tags))));
        }

        return array_values(array_unique(array_filter(array_map(
            fn ($tag) => Str::lower(trim($tag)),
            explode(',', (string) $tags),
        ))));
    }

    private function validateVariables(string $template, array $declaredVariables): void
    {
        preg_match_all('/{{\s*([a-zA-Z0-9_]+)\s*}}/', $template, $matches);
        $usedVariables = array_values(array_unique(array_map(fn ($variable) => Str::snake($variable), $matches[1] ?? [])));
        $missingDeclarations = array_diff($usedVariables, $declaredVariables);

        if ($missingDeclarations !== []) {
            throw ValidationException::withMessages([
                'variables' => 'Missing variable declarations: '.implode(', ', $missingDeclarations),
            ]);
        }
    }

    private function uniqueSlug(Workspace $workspace, Brand $brand, string $title): string
    {
        $base = Str::slug($title) ?: 'prompt';
        $slug = $base;
        $suffix = 2;

        while ($this->prompts->slugExists($workspace, $brand, $slug)) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
