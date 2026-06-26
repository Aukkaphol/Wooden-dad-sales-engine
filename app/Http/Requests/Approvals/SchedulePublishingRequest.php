<?php

namespace App\Http\Requests\Approvals;

use Illuminate\Foundation\Http\FormRequest;

class SchedulePublishingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'scheduled_at' => ['required', 'date', 'after:now'],
            'comment' => ['nullable', 'string', 'max:10000'],
            'reviewer_notes' => ['nullable', 'string', 'max:20000'],
        ];
    }
}
