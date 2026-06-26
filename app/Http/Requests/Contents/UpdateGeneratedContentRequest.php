<?php

namespace App\Http\Requests\Contents;

use App\Models\GeneratedContent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGeneratedContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('content')) ?? false;
    }

    public function rules(): array
    {
        $workspaceId = $this->route('workspace')?->getKey();

        return [
            'brand_id' => ['required', 'uuid', Rule::exists('brands', 'id')->where('workspace_id', $workspaceId)],
            'prompt_template_id' => ['required', 'uuid', Rule::exists('prompt_templates', 'id')->where('workspace_id', $workspaceId)],
            'asset_ids' => ['nullable', 'array'],
            'asset_ids.*' => ['uuid', Rule::exists('assets', 'id')->where('workspace_id', $workspaceId)],
            'title' => ['required', 'string', 'max:255'],
            'platform' => ['required', 'string', 'max:255'],
            'content_type' => ['required', Rule::in(GeneratedContent::TYPES)],
            'generated_content' => ['required', 'string', 'max:50000'],
            'variables' => ['nullable', 'array'],
            'variables.*' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', Rule::in([GeneratedContent::STATUS_DRAFT])],
            'tags' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:20000'],
        ];
    }
}
