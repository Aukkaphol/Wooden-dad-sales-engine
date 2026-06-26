<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Database\Factories\ContentApprovalHistoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContentApprovalHistory extends Model
{
    /** @use HasFactory<ContentApprovalHistoryFactory> */
    use HasFactory, HasUuid, SoftDeletes;

    public const DECISION_SUBMITTED = 'submitted';
    public const DECISION_APPROVED = 'approved';
    public const DECISION_REJECTED = 'rejected';
    public const DECISION_RETURNED = 'returned';
    public const DECISION_SCHEDULED = 'scheduled';
    public const DECISION_PUBLISHED = 'published';
    public const DECISION_ARCHIVED = 'archived';
    public const DECISION_OVERRIDDEN = 'overridden';

    protected $fillable = [
        'generated_content_id',
        'reviewer_id',
        'decision',
        'comment',
        'previous_status',
        'new_status',
        'decided_at',
    ];

    protected function casts(): array
    {
        return [
            'decided_at' => 'datetime',
        ];
    }

    public function generatedContent(): BelongsTo
    {
        return $this->belongsTo(GeneratedContent::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
