<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasUuid, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_path',
        'timezone',
        'locale',
        'last_login_at',
        'current_workspace_id',
        'status',
        'system_role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function ownedWorkspaces(): HasMany
    {
        return $this->hasMany(Workspace::class, 'owner_id');
    }

    public function currentWorkspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class, 'current_workspace_id');
    }

    public function workspaceMemberships(): HasMany
    {
        return $this->hasMany(WorkspaceUser::class);
    }

    public function uploadedAssets(): HasMany
    {
        return $this->hasMany(Asset::class, 'uploaded_by');
    }

    public function createdPromptTemplates(): HasMany
    {
        return $this->hasMany(PromptTemplate::class, 'created_by');
    }

    public function generatedContents(): HasMany
    {
        return $this->hasMany(GeneratedContent::class, 'created_by');
    }

    public function connectedSocialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class, 'connected_by');
    }

    public function workspaces(): BelongsToMany
    {
        return $this->belongsToMany(Workspace::class, 'workspace_users')
            ->withPivot(['id', 'role', 'permissions', 'invited_by', 'joined_at', 'deleted_at'])
            ->withTimestamps()
            ->wherePivotNull('deleted_at');
    }

    public function membershipFor(Workspace $workspace): ?WorkspaceUser
    {
        return $this->workspaceMemberships()
            ->where('workspace_id', $workspace->getKey())
            ->first();
    }

    public function hasWorkspaceRole(Workspace $workspace, array|string $roles): bool
    {
        $roles = (array) $roles;
        $membership = $this->membershipFor($workspace);

        return $membership !== null && in_array($membership->role, $roles, true);
    }

    public function isSystemAdmin(): bool
    {
        return $this->system_role === 'super_admin';
    }

    public function hasAnyWorkspace(): bool
    {
        return $this->workspaceMemberships()->exists();
    }
}
