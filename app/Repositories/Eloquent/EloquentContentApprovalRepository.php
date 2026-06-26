<?php

namespace App\Repositories\Eloquent;

use App\Models\ContentApprovalHistory;
use App\Models\GeneratedContent;
use App\Repositories\Contracts\ContentApprovalRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EloquentContentApprovalRepository implements ContentApprovalRepositoryInterface
{
    public function createHistory(GeneratedContent $content, array $attributes): ContentApprovalHistory
    {
        return $content->approvalHistories()->create($attributes);
    }

    public function historyForContent(GeneratedContent $content): Collection
    {
        return $content->approvalHistories()->with('reviewer')->latest('decided_at')->get();
    }
}
