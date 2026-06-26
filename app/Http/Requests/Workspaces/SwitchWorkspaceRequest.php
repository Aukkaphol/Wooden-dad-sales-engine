<?php

namespace App\Http\Requests\Workspaces;

use Illuminate\Foundation\Http\FormRequest;

class SwitchWorkspaceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('switch', $this->route('workspace')) ?? false;
    }

    public function rules(): array
    {
        return [];
    }
}
