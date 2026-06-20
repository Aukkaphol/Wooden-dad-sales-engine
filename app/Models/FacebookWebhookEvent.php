<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacebookWebhookEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_type',
        'payload_json',
        'leadgen_id',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'payload_json' => 'array',
            'processed_at' => 'datetime',
        ];
    }
}
