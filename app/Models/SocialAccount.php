<?php

namespace App\Models;

use App\Enums\SocialPlatform;
use App\Models\Concerns\HasUuid;
use Database\Factories\SocialAccountFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocialAccount extends Model
{
    /** @use HasFactory<SocialAccountFactory> */
    use HasFactory, HasUuid, SoftDeletes;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_CONNECTED = 'connected';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_REVOKED = 'revoked';

    public const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_CONNECTED,
        self::STATUS_EXPIRED,
        self::STATUS_REVOKED,
    ];

    protected $fillable = [
        'workspace_id',
        'brand_id',
        'connected_by',
        'platform',
        'provider_account_id',
        'name',
        'username',
        'avatar_url',
        'status',
        'scopes',
        'oauth_payload',
        'token_expires_at',
        'last_connected_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'platform' => SocialPlatform::class,
            'scopes' => 'array',
            'oauth_payload' => 'encrypted:array',
            'token_expires_at' => 'datetime',
            'last_connected_at' => 'datetime',
            'metadata' => 'array',
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

    public function connector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'connected_by');
    }
}
