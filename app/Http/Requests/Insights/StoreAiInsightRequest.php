<?php

namespace App\Http\Requests\Insights;

use App\Models\AiInsight;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAiInsightRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $value = $this->input('metadata');

        if (is_string($value) && trim($value) !== '') {
            $this->merge(['metadata' => json_decode($value, true) ?? $value]);
        }
    }

    public function authorize(): bool
    {
        return $this->user()?->can('create', [AiInsight::class, $this->route('workspace')]) ?? false;
    }

    public function rules(): array
    {
        $workspaceId = $this->route('workspace')?->getKey();

        return [
            'generated_content_id' => ['required', 'uuid', Rule::exists('generated_contents', 'id')->where('workspace_id', $workspaceId)],
            'analytics_record_id' => ['nullable', 'uuid', Rule::exists('analytics_records', 'id')->where('workspace_id', $workspaceId)],
            'insight_type' => ['required', 'string', Rule::in(AiInsight::TYPES)],
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['required', 'string', 'max:20000'],
            'recommendation' => ['nullable', 'string', 'max:20000'],
            'priority' => ['required', 'string', Rule::in(AiInsight::PRIORITIES)],
            'status' => ['nullable', 'string', Rule::in(AiInsight::STATUSES)],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
