<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class LineNotificationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'event',
        'channel',
        'recipient_id',
        'status',
        'notifiable_type',
        'notifiable_id',
        'message',
        'response_status',
        'error_message',
    ];

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }
}
