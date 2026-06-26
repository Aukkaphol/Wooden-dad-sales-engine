<?php

namespace App\Http\Requests\Pipeline;

use App\Models\MediaPipelineRun;
use App\Models\GeneratedContent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMediaPipelineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', [MediaPipelineRun::class, $this->route('workspace')]) ?? false;
    }

    public function rules(): array
    {
        $workspaceId = $this->route('workspace')?->getKey();

        return [
            'brand_id' => ['required', 'uuid', Rule::exists('brands', 'id')->where('workspace_id', $workspaceId)],
            'asset_ids' => ['nullable', 'array'],
            'asset_ids.*' => ['uuid', Rule::exists('assets', 'id')->where('workspace_id', $workspaceId)],
            'prompt_template_id' => ['required', 'uuid', Rule::exists('prompt_templates', 'id')->where('workspace_id', $workspaceId)],
            'title' => ['required', 'string', 'max:255'],
            'platform' => ['required', 'string', 'max:255'],
            'content_type' => ['required', 'string', Rule::in(GeneratedContent::TYPES)],
            'variables' => ['nullable', 'array'],
            'tags' => ['nullable'],
            'notes' => ['nullable', 'string', 'max:20000'],
        ];
    }
}
