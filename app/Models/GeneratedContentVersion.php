<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Database\Factories\GeneratedContentVersionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneratedContentVersion extends Model
{
    /** @use HasFactory<GeneratedContentVersionFactory> */
    use HasFactory, HasUuid;

    protected $fillable = [
        'generated_content_id',
        'created_by',
        'version',
        'title',
        'platform',
        'content_type',
        'prompt_snapshot',
        'variables',
        'generated_content',
        'status',
        'tags',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'variables' => 'array',
            'tags' => 'array',
            'version' => 'integer',
        ];
    }

    public function generatedContent(): BelongsTo
    {
        return $this->belongsTo(GeneratedContent::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
