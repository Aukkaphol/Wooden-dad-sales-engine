<?php

namespace App\Http\Controllers;

use App\Models\LineNotificationLog;
use App\Models\LineSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminLineSettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.line', [
            'lineSetting' => LineSetting::current(),
            'recentLogs' => LineNotificationLog::latest()->limit(8)->get(),
        ]);
    }

    public function logs(): View
    {
        return view('admin.settings.line-logs', [
            'logs' => LineNotificationLog::latest()->paginate(30),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'channel_access_token' => ['nullable', 'string', 'max:5000'],
            'admin_recipient_id' => ['nullable', 'string', 'max:255'],
            'production_group_id' => ['nullable', 'string', 'max:255'],
            'delivery_group_id' => ['nullable', 'string', 'max:255'],
            'notifications_enabled' => ['nullable', 'boolean'],
        ]);

        LineSetting::current()->update([
            'channel_access_token' => $validated['channel_access_token'] ?? null,
            'admin_recipient_id' => $validated['admin_recipient_id'] ?? null,
            'production_group_id' => $validated['production_group_id'] ?? null,
            'delivery_group_id' => $validated['delivery_group_id'] ?? null,
            'notifications_enabled' => (bool) ($validated['notifications_enabled'] ?? false),
        ]);

        return back()->with('success', 'บันทึกการตั้งค่า LINE OA เรียบร้อยแล้ว');
    }
}
