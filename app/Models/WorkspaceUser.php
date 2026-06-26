<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Database\Factories\WorkspaceUserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkspaceUser extends Model
{
    /** @use HasFactory<WorkspaceUserFactory> */
    use HasFactory, HasUuid, SoftDeletes;

    public const ROLE_OWNER = 'owner';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_MEMBER = 'member';
    public const ROLE_MARKETING_MANAGER = 'marketing_manager';
    public const ROLE_CONTENT_CREATOR = 'content_creator';
    public const ROLE_REVIEWER = 'reviewer';

    public const ROLES = [
        self::ROLE_OWNER,
        self::ROLE_ADMIN,
        self::ROLE_MEMBER,
        self::ROLE_MARKETING_MANAGER,
        self::ROLE_CONTENT_CREATOR,
        self::ROLE_REVIEWER,
    ];

    protected $table = 'workspace_users';

    protected $fillable = [
        'workspace_id',
        'user_id',
        'role',
        'permissions',
        'invited_by',
        'joined_at',
    ];

    protected function casts(): array
    {
        return [
            'permissions' => 'array',
            'joined_at' => 'datetime',
        ];
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }
}
