<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CompanySetting extends Model
{
    use HasFactory;

    public const CACHE_KEY = 'company_settings.current';

    protected $fillable = [
        'company_name',
        'brand_name',
        'phone',
        'email',
        'line_oa_url',
        'line_oa_id',
        'facebook_url',
        'instagram_url',
        'tiktok_url',
        'youtube_url',
        'website_url',
        'address',
        'province',
        'tax_id',
        'logo',
        'favicon',
        'primary_color',
        'secondary_color',
        'line_channel_id',
        'line_channel_secret',
        'line_channel_access_token',
        'line_staff_notify_user_id',
        'line_staff_group_id',
        'facebook_page_id',
        'facebook_access_token',
        'facebook_webhook_url',
        'google_analytics_measurement_id',
    ];

    public static function current(): self
    {
        return Cache::rememberForever(self::CACHE_KEY, function (): self {
            return self::query()->firstOrCreate([], self::defaults());
        });
    }

    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    public static function defaults(): array
    {
        return [
            'company_name' => 'Wooden Dad Design',
            'brand_name' => 'Wooden Dad Design',
            'phone' => '086-4299354',
            'email' => null,
            'line_oa_url' => null,
            'line_oa_id' => '@beerklung',
            'facebook_url' => null,
            'instagram_url' => null,
            'tiktok_url' => null,
            'youtube_url' => null,
            'website_url' => 'https://woodendaddesign.com',
            'address' => null,
            'province' => null,
            'tax_id' => null,
            'primary_color' => '#7a5634',
            'secondary_color' => '#f6ead8',
        ];
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->brand_name ?: $this->company_name ?: 'Wooden Dad Design';
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? asset('storage/'.$this->logo) : null;
    }

    public function getFaviconUrlAttribute(): ?string
    {
        return $this->favicon ? asset('storage/'.$this->favicon) : null;
    }

    public function getWebsiteDisplayAttribute(): string
    {
        if (! $this->website_url) {
            return '-';
        }

        return preg_replace('#^https?://#', '', rtrim($this->website_url, '/')) ?: $this->website_url;
    }

    public function getLineStaffRecipientAttribute(): ?string
    {
        return $this->line_staff_group_id ?: $this->line_staff_notify_user_id;
    }
}
