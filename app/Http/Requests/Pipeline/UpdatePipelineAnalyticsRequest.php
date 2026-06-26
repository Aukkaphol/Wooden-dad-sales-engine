<?php

namespace App\Http\Requests\Pipeline;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePipelineAnalyticsRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $value = $this->input('audience_breakdown');

        if (is_string($value) && trim($value) !== '') {
            $this->merge(['audience_breakdown' => json_decode($value, true) ?? $value]);
        }
    }

    public function authorize(): bool
    {
        return $this->user()?->can('manage', $this->route('pipeline')) ?? false;
    }

    public function rules(): array
    {
        return [
            'platform' => ['nullable', 'string', 'max:255'],
            'posted_at' => ['nullable', 'date'],
            'captured_at' => ['nullable', 'date'],
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
        ];
    }
}
