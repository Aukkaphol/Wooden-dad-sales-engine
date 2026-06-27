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
            'role' => ['required', Rule::in(WorkspaceUser::ADMIN_ASSIGNABLE_ROLES)],
        ];
    }
}
