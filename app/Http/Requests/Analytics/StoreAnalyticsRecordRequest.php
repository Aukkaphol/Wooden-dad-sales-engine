<?php

namespace App\Http\Requests\Analytics;

use App\Models\AnalyticsRecord;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAnalyticsRecordRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'audience_breakdown' => $this->decodeJsonField('audience_breakdown'),
            'metadata' => $this->decodeJsonField('metadata'),
        ]);
    }

    public function authorize(): bool
    {
        return $this->user()?->can('create', [AnalyticsRecord::class, $this->route('workspace')]) ?? false;
    }

    public function rules(): array
    {
        $workspaceId = $this->route('workspace')?->getKey();

        return [
            'generated_content_id' => ['required', 'uuid', Rule::exists('generated_contents', 'id')->where('workspace_id', $workspaceId)],
            'publishing_queue_item_id' => ['nullable', 'uuid', Rule::exists('publishing_queue_items', 'id')->where('workspace_id', $workspaceId)],
            'platform' => ['required', 'string', 'max:255'],
            'posted_at' => ['nullable', 'date'],
            'captured_at' => ['required', 'date'],
            'views' => ['nullable', 'integer', 'min:0'],
            'reach' => ['nullable', 'integer', 'min:0'],
            'impressions' => ['nullable', 'integer', 'min:0'],
            'likes' => ['nullable', 'integer', 'min:0'],
            'comments' => ['nullable', 'integer', 'min:0'],
            'shares' => ['nullable', 'integer', 'min:0'],
            'saves' => ['nullable', 'integer', 'min:0'],
            'follows_gained' => ['nullable', 'integer', 'min:0'],
            'link_clicks' => ['nullable', 'integer', 'min:0'],
            'estimated_revenue' => ['nullable', 'numeric', 'min:0'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:20000'],
            'audience_breakdown' => ['nullable', 'array'],
            'metadata' => ['nullable', 'array'],
        ];
    }

    private function decodeJsonField(string $field): mixed
    {
        $value = $this->input($field);

        if (! is_string($value) || trim($value) === '') {
            return $value;
        }

        return json_decode($value, true) ?? $value;
    }
}
