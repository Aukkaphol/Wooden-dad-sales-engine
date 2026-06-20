<?php

namespace App\Http\Controllers;

use App\Models\LineNotificationLog;
use App\Models\LineSetting;
use App\Models\CompanySetting;
use App\Services\LineNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminLineSettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.line', [
            'company' => CompanySetting::current(),
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
            'line_oa_id' => ['nullable', 'string', 'max:255'],
            'line_oa_url' => ['nullable', 'string', 'starts_with:https://', 'max:255'],
            'line_channel_id' => ['nullable', 'string', 'max:255'],
            'channel_access_token' => ['nullable', 'string', 'max:5000'],
            'line_channel_secret' => ['nullable', 'string', 'max:255'],
            'line_staff_notify_user_id' => ['nullable', 'string', 'max:255'],
            'line_staff_group_id' => ['nullable', 'string', 'max:255'],
            'admin_recipient_id' => ['nullable', 'string', 'max:255'],
            'production_group_id' => ['nullable', 'string', 'max:255'],
            'delivery_group_id' => ['nullable', 'string', 'max:255'],
            'notifications_enabled' => ['nullable', 'boolean'],
        ]);

        $company = CompanySetting::current();
        $company->update([
            'line_oa_id' => $validated['line_oa_id'] ?? null,
            'line_oa_url' => $validated['line_oa_url'] ?? null,
            'line_channel_id' => $validated['line_channel_id'] ?? null,
            'line_channel_access_token' => $validated['channel_access_token'] ?? null,
            'line_channel_secret' => $validated['line_channel_secret'] ?? null,
            'line_staff_notify_user_id' => $validated['line_staff_notify_user_id'] ?? null,
            'line_staff_group_id' => $validated['line_staff_group_id'] ?? null,
        ]);
        CompanySetting::clearCache();

        LineSetting::current()->update([
            'channel_access_token' => $validated['channel_access_token'] ?? null,
            'admin_recipient_id' => $validated['admin_recipient_id'] ?? ($validated['line_staff_group_id'] ?? $validated['line_staff_notify_user_id'] ?? null),
            'production_group_id' => $validated['production_group_id'] ?? null,
            'delivery_group_id' => $validated['delivery_group_id'] ?? null,
            'notifications_enabled' => (bool) ($validated['notifications_enabled'] ?? false),
        ]);

        return back()->with('success', 'บันทึกการตั้งค่า LINE OA เรียบร้อยแล้ว');
    }

    public function testNotification(LineNotificationService $lineNotificationService): RedirectResponse
    {
        $lineNotificationService->sendTestStaffNotification();

        return back()->with('success', 'ส่งข้อความทดสอบ LINE OA แล้ว กรุณาตรวจสอบประวัติการส่ง Notification');
    }
}
