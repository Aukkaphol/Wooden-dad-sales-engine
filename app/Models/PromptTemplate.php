<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Database\Factories\PromptTemplateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PromptTemplate extends Model
{
    /** @use HasFactory<PromptTemplateFactory> */
    use HasFactory, HasUuid, SoftDeletes;

    public const CATEGORY_FACEBOOK_POST = 'facebook_post';
    public const CATEGORY_FACEBOOK_REEL = 'facebook_reel';
    public const CATEGORY_TIKTOK = 'tiktok';
    public const CATEGORY_INSTAGRAM = 'instagram';
    public const CATEGORY_LINE_OA = 'line_oa';
    public const CATEGORY_PRODUCT_DESCRIPTION = 'product_description';
    public const CATEGORY_SEO_ARTICLE = 'seo_article';
    public const CATEGORY_BLOG = 'blog';
    public const CATEGORY_ADVERTISEMENT = 'advertisement';
    public const CATEGORY_THUMBNAIL = 'thumbnail';
    public const CATEGORY_IMAGE_GENERATION = 'image_generation';
    public const CATEGORY_VIDEO_SCRIPT = 'video_script';

    public const CATEGORIES = [
        self::CATEGORY_FACEBOOK_POST,
        self::CATEGORY_FACEBOOK_REEL,
        self::CATEGORY_TIKTOK,
        self::CATEGORY_INSTAGRAM,
        self::CATEGORY_LINE_OA,
        self::CATEGORY_PRODUCT_DESCRIPTION,
        self::CATEGORY_SEO_ARTICLE,
        self::CATEGORY_BLOG,
        self::CATEGORY_ADVERTISEMENT,
        self::CATEGORY_THUMBNAIL,
        self::CATEGORY_IMAGE_GENERATION,
        self::CATEGORY_VIDEO_SCRIPT,
    ];

    public const PLATFORM_FACEBOOK = 'facebook';
    public const PLATFORM_TIKTOK = 'tiktok';
    public const PLATFORM_INSTAGRAM = 'instagram';
    public const PLATFORM_LINE_OA = 'line_oa';
    public const PLATFORM_WEBSITE = 'website';
    public const PLATFORM_ADS = 'ads';
    public const PLATFORM_CREATIVE = 'creative';
    public const PLATFORM_VIDEO = 'video';

    public const PLATFORMS = [
        self::PLATFORM_FACEBOOK,
        self::PLATFORM_TIKTOK,
        self::PLATFORM_INSTAGRAM,
        self::PLATFORM_LINE_OA,
        self::PLATFORM_WEBSITE,
        self::PLATFORM_ADS,
        self::PLATFORM_CREATIVE,
        self::PLATFORM_VIDEO,
    ];

    public const STATUS_DRAFT = 'draft';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_ARCHIVED = 'archived';

    public const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_ACTIVE,
        self::STATUS_ARCHIVED,
    ];

    public const MODEL_GPT_55 = 'gpt-5.5';
    public const MODEL_GPT_5_THINKING = 'gpt-5-thinking';
    public const MODEL_CODEX = 'codex';
    public const MODEL_CLAUDE = 'claude';
    public const MODEL_GEMINI = 'gemini';

    public const MODELS = [
        self::MODEL_GPT_55,
        self::MODEL_GPT_5_THINKING,
        self::MODEL_CODEX,
        self::MODEL_CLAUDE,
        self::MODEL_GEMINI,
    ];

    protected $fillable = [
        'workspace_id',
        'brand_id',
        'created_by',
        'title',
        'slug',
        'category',
        'platform',
        'prompt_template',
        'variables',
        'example_output',
        'version',
        'status',
        'tags',
        'favorite',
        'usage_count',
        'success_rate',
        'rating_average',
        'rating_count',
        'recommended_model',
        'last_used_at',
    ];

    protected function casts(): array
    {
        return [
            'variables' => 'array',
            'tags' => 'array',
            'favorite' => 'boolean',
            'usage_count' => 'integer',
            'success_rate' => 'decimal:2',
            'rating_average' => 'decimal:2',
            'rating_count' => 'integer',
            'last_used_at' => 'datetime',
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(PromptTemplateVersion::class);
    }

    public function generatedContents(): HasMany
    {
        return $this->hasMany(GeneratedContent::class);
    }
}
