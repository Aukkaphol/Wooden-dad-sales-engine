<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Database\Factories\AssetFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    /** @use HasFactory<AssetFactory> */
    use HasFactory, HasUuid, SoftDeletes;

    public const TYPE_IMAGE = 'image';
    public const TYPE_VIDEO = 'video';
    public const TYPE_AUDIO = 'audio';
    public const TYPE_LOGO = 'logo';
    public const TYPE_DOCUMENT = 'document';
    public const TYPE_TEMPLATE = 'template';

    public const TYPES = [
        self::TYPE_IMAGE,
        self::TYPE_VIDEO,
        self::TYPE_AUDIO,
        self::TYPE_LOGO,
        self::TYPE_DOCUMENT,
        self::TYPE_TEMPLATE,
    ];

    public const STATUS_DRAFT = 'draft';
    public const STATUS_READY = 'ready';
    public const STATUS_ARCHIVED = 'archived';

    public const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_READY,
        self::STATUS_ARCHIVED,
    ];

    protected $fillable = [
        'workspace_id',
        'brand_id',
        'uploaded_by',
        'name',
        'type',
        'mime_type',
        'disk',
        'path',
        'thumbnail_path',
        'extension',
        'size_bytes',
        'width',
        'height',
        'duration_seconds',
        'metadata',
        'tags',
        'category',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'tags' => 'array',
            'size_bytes' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
            'duration_seconds' => 'integer',
        ];
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function generatedContents(): BelongsToMany
    {
        return $this->belongsToMany(GeneratedContent::class, 'generated_content_assets')->withTimestamps();
    }
}
