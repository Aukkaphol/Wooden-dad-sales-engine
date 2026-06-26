<?php

namespace App\Http\Requests\Pipeline;

use Illuminate\Foundation\Http\FormRequest;

class QueueMediaPipelineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage', $this->route('pipeline')) ?? false;
    }

    public function rules(): array
    {
        return [
            'platform' => ['nullable', 'string', 'max:255'],
            'scheduled_at' => ['nullable', 'date', 'after:now'],
            'priority' => ['nullable', 'integer', 'min:1', 'max:999'],
            'comment' => ['nullable', 'string', 'max:10000'],
        ];
    }
}
