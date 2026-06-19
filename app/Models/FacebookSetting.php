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
        'webhook_enabled',
        'lead_ads_enabled',
        'messenger_enabled',
        'active',
    ];

    protected $casts = [
        'webhook_enabled' => 'boolean',
        'lead_ads_enabled' => 'boolean',
        'messenger_enabled' => 'boolean',
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
        if (! $this->page_access_token) {
            return '-';
        }

        $length = mb_strlen($this->page_access_token);

        if ($length <= 12) {
            return str_repeat('*', $length);
        }

        return mb_substr($this->page_access_token, 0, 6).'******'.mb_substr($this->page_access_token, -4);
    }
}
