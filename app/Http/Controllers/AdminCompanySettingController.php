<?php

namespace App\Http\Controllers;

use App\Models\CompanySetting;
use App\Models\FacebookSetting;
use App\Models\LineSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminCompanySettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.company', [
            'company' => CompanySetting::current(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $company = CompanySetting::current();

        $validated = $request->validate([
            'company_name' => ['nullable', 'string', 'max:255'],
            'brand_name' => ['nullable', 'string', 'max:255'],
            'tax_id' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:2000'],
            'province' => ['nullable', 'string', 'max:255'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'line_oa_id' => ['nullable', 'string', 'max:255'],
            'line_oa_url' => ['nullable', 'string', 'starts_with:https://', 'max:255'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'tiktok_url' => ['nullable', 'url', 'max:255'],
            'youtube_url' => ['nullable', 'url', 'max:255'],
            'primary_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:5120'],
            'favicon' => ['nullable', 'image', 'mimes:ico,jpg,jpeg,png,webp,svg', 'max:1024'],
            'line_channel_id' => ['nullable', 'string', 'max:255'],
            'line_channel_secret' => ['nullable', 'string', 'max:255'],
            'line_channel_access_token' => ['nullable', 'string', 'max:5000'],
            'line_staff_notify_user_id' => ['nullable', 'string', 'max:255'],
            'line_staff_group_id' => ['nullable', 'string', 'max:255'],
            'facebook_page_id' => ['nullable', 'string', 'max:255'],
            'facebook_access_token' => ['nullable', 'string', 'max:5000'],
            'facebook_webhook_url' => ['nullable', 'url', 'max:255'],
            'google_analytics_measurement_id' => ['nullable', 'string', 'max:255'],
        ], $this->messages());

        foreach (['logo', 'favicon'] as $field) {
            if ($request->hasFile($field)) {
                if ($company->{$field}) {
                    Storage::disk('public')->delete($company->{$field});
                }

                $validated[$field] = $request->file($field)->store('company', 'public');
            } else {
                unset($validated[$field]);
            }
        }

        $company->update($validated);
        $this->syncIntegrationSettings($company);
        CompanySetting::clearCache();

        return back()->with('success', 'บันทึกข้อมูลบริษัทเรียบร้อยแล้ว');
    }

    private function syncIntegrationSettings(CompanySetting $company): void
    {
        $lineSetting = LineSetting::current();

        if ($company->line_channel_access_token && ! $lineSetting->channel_access_token) {
            $lineSetting->update([
                'channel_access_token' => $company->line_channel_access_token,
            ]);
        }

        $facebookSetting = FacebookSetting::current();
        $facebookUpdates = [];

        if ($company->facebook_page_id && ! $facebookSetting->page_id) {
            $facebookUpdates['page_id'] = $company->facebook_page_id;
        }

        if ($company->facebook_access_token && ! $facebookSetting->page_access_token) {
            $facebookUpdates['page_access_token'] = $company->facebook_access_token;
        }

        if ($facebookUpdates !== []) {
            $facebookSetting->update($facebookUpdates);
        }
    }

    private function messages(): array
    {
        return [
            'email.email' => 'กรุณากรอกอีเมลให้ถูกต้อง',
            '*.url' => 'กรุณากรอก URL ให้ถูกต้อง',
            'line_oa_url.starts_with' => 'LINE OA URL ต้องขึ้นต้นด้วย https://',
            'primary_color.regex' => 'สีหลักต้องอยู่ในรูปแบบ #RRGGBB',
            'secondary_color.regex' => 'สีรองต้องอยู่ในรูปแบบ #RRGGBB',
            'logo.image' => 'กรุณาอัปโหลดไฟล์โลโก้เป็นรูปภาพเท่านั้น',
            'logo.mimes' => 'โลโก้รองรับ jpg, png, webp หรือ svg เท่านั้น',
            'logo.max' => 'ไฟล์โลโก้ต้องไม่เกิน 5MB',
            'favicon.image' => 'กรุณาอัปโหลด favicon เป็นรูปภาพเท่านั้น',
            'favicon.mimes' => 'favicon รองรับ ico, jpg, png, webp หรือ svg เท่านั้น',
            'favicon.max' => 'ไฟล์ favicon ต้องไม่เกิน 1MB',
        ];
    }
}
