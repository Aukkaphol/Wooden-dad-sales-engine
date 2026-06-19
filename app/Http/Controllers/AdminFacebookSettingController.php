<?php

namespace App\Http\Controllers;

use App\Models\FacebookSetting;
use App\Models\FacebookWebhookLog;
use App\Services\FacebookService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminFacebookSettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.facebook', [
            'setting' => FacebookSetting::current(),
            'latestLog' => FacebookWebhookLog::query()->latest()->first(),
        ]);
    }

    public function update(Request $request, FacebookService $facebookService): RedirectResponse
    {
        $setting = FacebookSetting::current();

        if ($request->input('action') === 'test') {
            $result = $facebookService->testConnection($setting);

            return back()->with($result['ok'] ? 'success' : 'error', $result['message']);
        }

        $validated = $request->validate([
            'page_name' => ['nullable', 'string', 'max:255'],
            'page_id' => ['nullable', 'string', 'max:255'],
            'page_access_token' => ['nullable', 'string'],
            'app_id' => ['nullable', 'string', 'max:255'],
            'app_secret' => ['nullable', 'string', 'max:255'],
            'webhook_verify_token' => ['nullable', 'string', 'max:255'],
            'webhook_enabled' => ['nullable', 'boolean'],
            'lead_ads_enabled' => ['nullable', 'boolean'],
            'messenger_enabled' => ['nullable', 'boolean'],
            'active' => ['nullable', 'boolean'],
        ], $this->messages());

        if (blank($validated['page_access_token'] ?? null)) {
            unset($validated['page_access_token']);
        }

        $setting->update([
            ...$validated,
            'webhook_enabled' => (bool) ($validated['webhook_enabled'] ?? false),
            'lead_ads_enabled' => (bool) ($validated['lead_ads_enabled'] ?? false),
            'messenger_enabled' => (bool) ($validated['messenger_enabled'] ?? false),
            'active' => (bool) ($validated['active'] ?? false),
        ]);

        return back()->with('success', 'บันทึกการเชื่อมต่อ Facebook เรียบร้อยแล้ว');
    }

    private function messages(): array
    {
        return [
            'page_name.max' => 'ชื่อเพจต้องไม่เกิน 255 ตัวอักษร',
            'page_id.max' => 'Page ID ต้องไม่เกิน 255 ตัวอักษร',
            'app_id.max' => 'App ID ต้องไม่เกิน 255 ตัวอักษร',
            'app_secret.max' => 'App Secret ต้องไม่เกิน 255 ตัวอักษร',
            'webhook_verify_token.max' => 'Webhook Verify Token ต้องไม่เกิน 255 ตัวอักษร',
        ];
    }
}
