<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacebookWebhookLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_type',
        'page_id',
        'payload',
        'status',
        'error_message',
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}
