<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacebookSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_name',
        'page_id',
        'page_access_token',
        'app_id',
        'app_secret',
        'webhook_verify_token',
        'facebook_app_id',
        'facebook_app_secret',
        'facebook_page_id',
        'facebook_page_access_token',
        'facebook_webhook_verify_token',
        'facebook_webhook_callback_url',
        'facebook_enabled',
        'facebook_last_synced_at',
        'webhook_enabled',
        'lead_ads_enabled',
        'messenger_enabled',
        'active',
    ];

    protected $casts = [
        'webhook_enabled' => 'boolean',
        'lead_ads_enabled' => 'boolean',
        'messenger_enabled' => 'boolean',
        'facebook_enabled' => 'boolean',
        'facebook_last_synced_at' => 'datetime',
        'active' => 'boolean',
    ];

    public static function current(): self
    {
        return self::query()->firstOrCreate([], [
            'webhook_verify_token' => bin2hex(random_bytes(16)),
            'active' => true,
        ]);
    }

    public function getMaskedPageAccessTokenAttribute(): string
    {
        $token = $this->facebook_page_access_token ?: $this->page_access_token;

        if (! $token) {
            return '-';
        }

        $length = mb_strlen($token);

        if ($length <= 12) {
            return str_repeat('*', $length);
        }

        return mb_substr($token, 0, 6).'******'.mb_substr($token, -4);
    }

    public function getEffectivePageIdAttribute(): ?string
    {
        return $this->facebook_page_id ?: $this->page_id;
    }

    public function getEffectivePageAccessTokenAttribute(): ?string
    {
        return $this->facebook_page_access_token ?: $this->page_access_token;
    }

    public function getEffectiveVerifyTokenAttribute(): ?string
    {
        return $this->facebook_webhook_verify_token ?: $this->webhook_verify_token;
    }

    public function getEffectiveAppIdAttribute(): ?string
    {
        return $this->facebook_app_id ?: $this->app_id;
    }
}
