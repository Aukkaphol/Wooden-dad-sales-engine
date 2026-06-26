<?php

namespace App\Http\Controllers;

use App\Services\Dashboard\DashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, DashboardService $dashboard): View
    {
        return view('dashboard', $dashboard->data($request->user(), $request->only([
            'workspace_id',
            'brand_id',
            'q',
            'content_status',
            'platform',
        ])));
    }
}
