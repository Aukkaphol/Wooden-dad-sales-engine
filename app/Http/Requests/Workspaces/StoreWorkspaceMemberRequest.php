<?php

namespace App\Http\Requests\Workspaces;

use App\Models\WorkspaceUser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWorkspaceMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manageMembers', $this->route('workspace')) ?? false;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email:rfc', 'exists:users,email'],
            'role' => ['required', Rule::in([
                WorkspaceUser::ROLE_ADMIN,
                WorkspaceUser::ROLE_MEMBER,
                WorkspaceUser::ROLE_MARKETING_MANAGER,
                WorkspaceUser::ROLE_CONTENT_CREATOR,
                WorkspaceUser::ROLE_REVIEWER,
            ])],
        ];
    }
}
