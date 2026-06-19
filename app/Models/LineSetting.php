<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LineSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel_access_token',
        'admin_recipient_id',
        'production_group_id',
        'delivery_group_id',
        'notifications_enabled',
    ];

    protected function casts(): array
    {
        return [
            'notifications_enabled' => 'boolean',
        ];
    }

    public static function current(): self
    {
        return self::query()->firstOrCreate([], [
            'channel_access_token' => config('services.line.channel_access_token'),
            'admin_recipient_id' => config('services.line.group_id') ?: config('services.line.user_id'),
            'production_group_id' => null,
            'delivery_group_id' => null,
            'notifications_enabled' => false,
        ]);
    }
}
