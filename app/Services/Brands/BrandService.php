<?php

namespace App\Services\Brands;

use App\Models\Brand;
use App\Models\User;
use App\Models\Workspace;
use App\Repositories\Contracts\BrandRepositoryInterface;
use App\Services\ActivityLogService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BrandService
{
    public function __construct(
        private readonly BrandRepositoryInterface $brands,
        private readonly ActivityLogService $activityLog,
    ) {
    }

    public function listForWorkspace(Workspace $workspace, int $perPage = 15): LengthAwarePaginator
    {
        return $this->brands->paginateForWorkspace($workspace, $perPage);
    }

    public function create(User $actor, Workspace $workspace, array $attributes, Request $request): Brand
    {
        return DB::transaction(function () use ($actor, $workspace, $attributes, $request): Brand {
            $logo = $this->pullLogo($attributes);

            $brand = $this->brands->create($workspace, $this->payload($attributes) + [
                'slug' => $this->uniqueSlug($workspace, $attributes['name']),
                'status' => 'active',
            ]);

            if ($logo) {
                $brand = $this->brands->update($brand, [
                    'logo_path' => $this->storeLogo($workspace, $brand, $logo),
                ]);
            }

            $this->activityLog->queue(
                event: 'brand.created',
                description: 'Brand created.',
                subject: $brand,
                request: $request,
                userId: $actor->getKey(),
            );

            return $brand;
        });
    }

    public function update(User $actor, Brand $brand, array $attributes, Request $request): Brand
    {
        return DB::transaction(function () use ($actor, $brand, $attributes, $request): Brand {
            $logo = $this->pullLogo($attributes);
            $payload = $this->payload($attributes) + [
                'status' => $attributes['status'],
            ];

            if ($logo) {
                $this->deleteLogo($brand);
                $payload['logo_path'] = $this->storeLogo($brand->workspace, $brand, $logo);
            }

            $updated = $this->brands->update($brand, $payload);

            $this->activityLog->queue(
                event: 'brand.updated',
                description: 'Brand updated.',
                subject: $updated,
                request: $request,
                userId: $actor->getKey(),
            );

            return $updated;
        });
    }

    public function delete(User $actor, Brand $brand, Request $request): void
    {
        DB::transaction(function () use ($actor, $brand, $request): void {
            $this->brands->delete($brand);

            $this->activityLog->queue(
                event: 'brand.deleted',
                description: 'Brand deleted.',
                subject: $brand,
                request: $request,
                userId: $actor->getKey(),
            );
        });
    }

    private function payload(array $attributes): array
    {
        return [
            'name' => $attributes['name'],
            'primary_color' => $attributes['primary_color'] ?? null,
            'secondary_color' => $attributes['secondary_color'] ?? null,
            'font_family' => $attributes['font_family'] ?? null,
            'tone' => $attributes['tone'] ?? null,
            'voice' => $attributes['voice'] ?? null,
            'default_prompt' => $attributes['default_prompt'] ?? null,
            'default_cta' => $attributes['default_cta'] ?? null,
            'contact_information' => $this->cleanArray($attributes['contact_information'] ?? []),
            'social_links' => $this->cleanArray($attributes['social_links'] ?? []),
        ];
    }

    private function cleanArray(array $values): array
    {
        return array_filter($values, fn ($value) => filled($value));
    }

    private function pullLogo(array &$attributes): ?UploadedFile
    {
        $logo = $attributes['logo'] ?? null;
        unset($attributes['logo']);

        return $logo instanceof UploadedFile ? $logo : null;
    }

    private function storeLogo(Workspace $workspace, Brand $brand, UploadedFile $logo): string
    {
        return $logo->storeAs(
            "workspaces/{$workspace->getKey()}/brands/{$brand->getKey()}/logos",
            Str::uuid().'.'.$logo->getClientOriginalExtension(),
            'public',
        );
    }

    private function deleteLogo(Brand $brand): void
    {
        if ($brand->logo_path) {
            Storage::disk('public')->delete($brand->logo_path);
        }
    }

    private function uniqueSlug(Workspace $workspace, string $name): string
    {
        $base = Str::slug($name) ?: 'brand';
        $slug = $base;
        $suffix = 2;

        while ($this->brands->slugExists($workspace, $slug)) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
