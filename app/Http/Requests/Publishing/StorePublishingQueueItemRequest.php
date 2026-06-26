<?php

namespace App\Http\Requests\Publishing;

use App\Models\PublishingQueueItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePublishingQueueItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', [PublishingQueueItem::class, $this->route('workspace')]) ?? false;
    }

    public function rules(): array
    {
        $workspaceId = $this->route('workspace')?->getKey();

        return [
            'generated_content_id' => ['required', 'uuid', Rule::exists('generated_contents', 'id')->where('workspace_id', $workspaceId)],
            'platform' => ['required', 'string', 'max:255'],
            'scheduled_at' => ['nullable', 'date', 'after:now'],
            'priority' => ['required', 'integer', 'min:1', 'max:999'],
            'comment' => ['nullable', 'string', 'max:10000'],
        ];
    }
}
