<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Database\Factories\PromptTemplateVersionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromptTemplateVersion extends Model
{
    /** @use HasFactory<PromptTemplateVersionFactory> */
    use HasFactory, HasUuid;

    protected $fillable = [
        'prompt_template_id',
        'created_by',
        'version',
        'title',
        'category',
        'platform',
        'prompt_template',
        'variables',
        'example_output',
        'status',
        'tags',
        'recommended_model',
    ];

    protected function casts(): array
    {
        return [
            'variables' => 'array',
            'tags' => 'array',
            'version' => 'integer',
        ];
    }

    public function promptTemplate(): BelongsTo
    {
        return $this->belongsTo(PromptTemplate::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
