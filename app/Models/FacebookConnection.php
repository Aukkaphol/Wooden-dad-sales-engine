<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FacebookConnection extends Model
{
    use HasUuid, SoftDeletes;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_REVOKED = 'revoked';

    public const CONNECTION_ACTIVE = 'active';
    public const CONNECTION_NEEDS_REFRESH = 'needs_refresh';
    public const CONNECTION_DISCONNECTED = 'disconnected';
    public const CONNECTION_ERROR = 'error';

    public const CONNECTION_STATUSES = [
        self::CONNECTION_ACTIVE,
        self::CONNECTION_NEEDS_REFRESH,
        self::CONNECTION_DISCONNECTED,
        self::CONNECTION_ERROR,
    ];

    protected $fillable = [
        'workspace_id',
        'facebook_user_id',
        'facebook_user_name',
        'facebook_user_avatar',
        'page_id',
        'page_name',
        'page_avatar',
        'page_category',
        'page_followers_count',
        'page_likes_count',
        'page_verification_status',
        'page_access_token',
        'token_expires_at',
        'permissions',
        'status',
        'connection_status',
        'last_synced_at',
        'last_tested_at',
        'last_error',
    ];

    protected $attributes = [
        'status' => self::STATUS_ACTIVE,
        'connection_status' => self::CONNECTION_ACTIVE,
    ];

    protected function casts(): array
    {
        return [
            'page_access_token' => 'encrypted',
            'token_expires_at' => 'datetime',
            'permissions' => 'array',
            'last_synced_at' => 'datetime',
            'last_tested_at' => 'datetime',
        ];
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(FacebookLog::class);
    }
}
