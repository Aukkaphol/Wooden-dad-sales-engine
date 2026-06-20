<?php

namespace App\Http\Controllers;

use App\Models\FacebookSetting;
use App\Models\FacebookWebhookEvent;
use App\Models\FacebookWebhookLog;
use App\Services\FacebookService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminFacebookSettingController extends Controller
{
    public function edit(): View
    {
        $setting = FacebookSetting::current();
        $callbackUrl = rtrim((string) config('app.url'), '/').route('webhooks.facebook.receive', [], false);

        return view('admin.settings.facebook', [
            'setting' => $setting,
            'callbackUrl' => $setting->facebook_webhook_callback_url ?: $callbackUrl,
            'latestEvent' => FacebookWebhookEvent::query()->latest()->first(),
            'latestLog' => FacebookWebhookLog::query()->latest()->first(),
        ]);
    }

    public function update(Request $request, FacebookService $facebookService): RedirectResponse
    {
        $setting = FacebookSetting::current();

        if ($request->input('action') === 'test') {
            $result = $facebookService->testConnection($setting);

            if ($result['ok']) {
                $setting->update(['facebook_last_synced_at' => now()]);
            }

            return back()->with($result['ok'] ? 'success' : 'error', $result['message']);
        }

        $validated = $request->validate([
            'page_name' => ['nullable', 'string', 'max:255'],
            'facebook_app_id' => ['nullable', 'string', 'max:255'],
            'facebook_app_secret' => ['nullable', 'string', 'max:255'],
            'facebook_page_id' => ['nullable', 'string', 'max:255'],
            'facebook_page_access_token' => ['nullable', 'string', 'max:5000'],
            'facebook_webhook_verify_token' => ['nullable', 'string', 'max:255'],
            'facebook_webhook_callback_url' => ['nullable', 'url', 'max:255'],
            'facebook_enabled' => ['nullable', 'boolean'],
        ], $this->messages());

        if (blank($validated['facebook_page_access_token'] ?? null)) {
            unset($validated['facebook_page_access_token']);
        }

        if (blank($validated['facebook_app_secret'] ?? null)) {
            unset($validated['facebook_app_secret']);
        }

        $callbackUrl = rtrim((string) config('app.url'), '/').route('webhooks.facebook.receive', [], false);
        $setting->update([
            ...$validated,
            'facebook_webhook_callback_url' => $validated['facebook_webhook_callback_url'] ?? $callbackUrl,
            'facebook_enabled' => (bool) ($validated['facebook_enabled'] ?? false),
            'page_name' => $validated['page_name'] ?? $setting->page_name,
            'page_id' => $validated['facebook_page_id'] ?? $setting->page_id,
            'page_access_token' => $validated['facebook_page_access_token'] ?? $setting->page_access_token,
            'app_id' => $validated['facebook_app_id'] ?? $setting->app_id,
            'app_secret' => $validated['facebook_app_secret'] ?? $setting->app_secret,
            'webhook_verify_token' => $validated['facebook_webhook_verify_token'] ?? $setting->webhook_verify_token,
            'webhook_enabled' => (bool) ($validated['facebook_enabled'] ?? false),
            'lead_ads_enabled' => (bool) ($validated['facebook_enabled'] ?? false),
            'active' => (bool) ($validated['facebook_enabled'] ?? false),
        ]);

        return back()->with('success', 'บันทึกการเชื่อมต่อ Facebook เรียบร้อยแล้ว');
    }

    private function messages(): array
    {
        return [
            'facebook_webhook_callback_url.url' => 'Webhook Callback URL ต้องเป็น URL ที่ถูกต้อง',
            'facebook_app_id.max' => 'Facebook App ID ต้องไม่เกิน 255 ตัวอักษร',
            'facebook_page_id.max' => 'Facebook Page ID ต้องไม่เกิน 255 ตัวอักษร',
            'facebook_webhook_verify_token.max' => 'Webhook Verify Token ต้องไม่เกิน 255 ตัวอักษร',
        ];
    }
}
