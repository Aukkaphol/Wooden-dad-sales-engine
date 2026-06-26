<?php

namespace App\Repositories\Contracts;

use App\Models\ContentApprovalHistory;
use App\Models\GeneratedContent;
use Illuminate\Database\Eloquent\Collection;

interface ContentApprovalRepositoryInterface
{
    public function createHistory(GeneratedContent $content, array $attributes): ContentApprovalHistory;

    public function historyForContent(GeneratedContent $content): Collection;
}
