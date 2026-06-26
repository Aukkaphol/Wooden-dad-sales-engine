<?php

namespace App\Services\Assets;

use App\Models\Asset;
use App\Models\Brand;
use App\Models\User;
use App\Models\Workspace;
use App\Repositories\Contracts\AssetRepositoryInterface;
use App\Services\ActivityLogService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AssetService
{
    public function __construct(
        private readonly AssetRepositoryInterface $assets,
        private readonly ActivityLogService $activityLog,
    ) {
    }

    public function search(Workspace $workspace, array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->assets->search($workspace, $filters, $perPage);
    }

    public function create(User $actor, Workspace $workspace, Brand $brand, array $attributes, Request $request): Asset
    {
        return DB::transaction(function () use ($actor, $workspace, $brand, $attributes, $request): Asset {
            /** @var UploadedFile $file */
            $file = $attributes['file'];
            $type = $this->detectType($file);
            $assetId = (string) Str::uuid();
            $path = $this->storeFile($workspace, $brand, $assetId, $file);

            $asset = $this->assets->create([
                'id' => $assetId,
                'workspace_id' => $workspace->getKey(),
                'brand_id' => $brand->getKey(),
                'uploaded_by' => $actor->getKey(),
                'name' => $attributes['name'],
                'type' => $type,
                'mime_type' => $file->getMimeType() ?: $file->getClientMimeType(),
                'disk' => 'local',
                'path' => $path,
                'thumbnail_path' => null,
                'extension' => $file->getClientOriginalExtension(),
                'size_bytes' => $file->getSize(),
                'width' => $this->imageWidth($file, $type),
                'height' => $this->imageHeight($file, $type),
                'duration_seconds' => null,
                'metadata' => $this->metadata($file),
                'tags' => $this->tags($attributes['tags'] ?? ''),
                'category' => $attributes['category'] ?? null,
                'status' => $attributes['status'],
            ]);

            $this->activityLog->queue(
                event: 'asset.created',
                description: 'Asset uploaded.',
                subject: $asset,
                request: $request,
                userId: $actor->getKey(),
            );

            return $asset;
        });
    }

    public function update(User $actor, Asset $asset, Brand $brand, array $attributes, Request $request): Asset
    {
        return DB::transaction(function () use ($actor, $asset, $brand, $attributes, $request): Asset {
            $payload = [
                'brand_id' => $brand->getKey(),
                'name' => $attributes['name'],
                'tags' => $this->tags($attributes['tags'] ?? ''),
                'category' => $attributes['category'] ?? null,
                'status' => $attributes['status'],
                'metadata' => array_replace($asset->metadata ?? [], $attributes['metadata'] ?? []),
            ];

            $file = $attributes['file'] ?? null;

            if ($file instanceof UploadedFile) {
                $this->deleteStoredFile($asset);
                $type = $this->detectType($file);
                $payload += [
                    'type' => $type,
                    'mime_type' => $file->getMimeType() ?: $file->getClientMimeType(),
                    'disk' => 'local',
                    'path' => $this->storeFile($asset->workspace, $brand, $asset->getKey(), $file),
                    'thumbnail_path' => null,
                    'extension' => $file->getClientOriginalExtension(),
                    'size_bytes' => $file->getSize(),
                    'width' => $this->imageWidth($file, $type),
                    'height' => $this->imageHeight($file, $type),
                    'duration_seconds' => null,
                    'metadata' => array_replace($attributes['metadata'] ?? [], $this->metadata($file)),
                ];
            }

            $updated = $this->assets->update($asset, $payload);

            $this->activityLog->queue(
                event: 'asset.updated',
                description: 'Asset updated.',
                subject: $updated,
                request: $request,
                userId: $actor->getKey(),
            );

            return $updated;
        });
    }

    public function delete(User $actor, Asset $asset, Request $request): void
    {
        DB::transaction(function () use ($actor, $asset, $request): void {
            $this->assets->delete($asset);

            $this->activityLog->queue(
                event: 'asset.deleted',
                description: 'Asset deleted.',
                subject: $asset,
                request: $request,
                userId: $actor->getKey(),
            );
        });
    }

    private function detectType(UploadedFile $file): string
    {
        $mimeType = $file->getMimeType() ?: $file->getClientMimeType();
        $extension = Str::lower($file->getClientOriginalExtension());

        if (Str::startsWith($mimeType, 'image/')) {
            return in_array($extension, ['svg'], true) || Str::contains($file->getClientOriginalName(), 'logo')
                ? Asset::TYPE_LOGO
                : Asset::TYPE_IMAGE;
        }

        if (Str::startsWith($mimeType, 'video/')) {
            return Asset::TYPE_VIDEO;
        }

        if (Str::startsWith($mimeType, 'audio/')) {
            return Asset::TYPE_AUDIO;
        }

        if (in_array($extension, ['zip'], true)) {
            return Asset::TYPE_TEMPLATE;
        }

        if (in_array($mimeType, [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
            'text/csv',
        ], true)) {
            return Asset::TYPE_DOCUMENT;
        }

        throw ValidationException::withMessages([
            'file' => 'Unsupported file type.',
        ]);
    }

    private function storeFile(Workspace $workspace, Brand $brand, string $assetId, UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $filename = Str::uuid().($extension ? ".{$extension}" : '');

        return $file->storeAs(
            "workspaces/{$workspace->getKey()}/brands/{$brand->getKey()}/assets/{$assetId}",
            $filename,
            'local',
        );
    }

    private function deleteStoredFile(Asset $asset): void
    {
        Storage::disk($asset->disk)->delete($asset->path);
    }

    private function imageWidth(UploadedFile $file, string $type): ?int
    {
        if (! in_array($type, [Asset::TYPE_IMAGE, Asset::TYPE_LOGO], true) || $file->getMimeType() === 'image/svg+xml') {
            return null;
        }

        $size = @getimagesize($file->getRealPath());

        return $size ? (int) $size[0] : null;
    }

    private function imageHeight(UploadedFile $file, string $type): ?int
    {
        if (! in_array($type, [Asset::TYPE_IMAGE, Asset::TYPE_LOGO], true) || $file->getMimeType() === 'image/svg+xml') {
            return null;
        }

        $size = @getimagesize($file->getRealPath());

        return $size ? (int) $size[1] : null;
    }

    private function metadata(UploadedFile $file): array
    {
        return [
            'original_name' => $file->getClientOriginalName(),
            'client_mime_type' => $file->getClientMimeType(),
        ];
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
}
