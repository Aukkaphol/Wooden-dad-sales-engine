<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacebookIntegrationSetting extends Model
{
    use HasUuid;

    protected $fillable = [
        'workspace_id',
        'app_id',
        'app_secret',
        'redirect_uri',
    ];

    protected function casts(): array
    {
        return [
            'app_secret' => 'encrypted',
        ];
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }
}
