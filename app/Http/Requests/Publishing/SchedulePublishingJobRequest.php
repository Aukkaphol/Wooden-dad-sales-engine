<?php

namespace App\Http\Requests\Publishing;

use App\Models\PublishingQueueItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SchedulePublishingJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage', $this->route('publishing')) ?? false;
    }

    public function rules(): array
    {
        $workspaceId = $this->route('workspace')?->getKey();

        return [
            'social_account_id' => ['nullable', 'uuid', Rule::exists('social_accounts', 'id')->where('workspace_id', $workspaceId)],
            'scheduled_at' => ['nullable', 'date', 'after:now'],
            'comment' => ['nullable', 'string', 'max:10000'],
        ];
    }

    public function queueItem(): ?PublishingQueueItem
    {
        return $this->route('publishing');
    }
}
