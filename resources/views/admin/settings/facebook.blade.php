@extends('layouts.admin', ['title' => 'เชื่อมต่อ Facebook | '.company()->display_name])

@section('content')
<section class="bg-pine-50">
    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-semibold text-pine-500">Marketing Integration</p>
                <h1 class="mt-2 text-3xl font-semibold text-ink">เชื่อมต่อ Facebook Page API</h1>
                <p class="mt-2 text-sm leading-6 text-pine-700">ตั้งค่า Facebook Lead Ads Webhook เพื่อรับลีดเข้าสู่ CRM Pipeline เดียวกับ Website และ LINE OA</p>
            </div>
            <a href="{{ route('admin.marketing.facebook-leads') }}" class="w-fit rounded-xl bg-white px-4 py-2 text-sm font-semibold text-pine-700 ring-1 ring-pine-200 hover:bg-pine-100">ดู Facebook Leads</a>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-xl bg-green-50 p-4 text-sm font-medium text-green-800 ring-1 ring-green-600/20">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-xl bg-rose-50 p-4 text-sm font-medium text-rose-800 ring-1 ring-rose-600/20">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-xl bg-rose-50 p-4 text-sm font-medium text-rose-800 ring-1 ring-rose-600/20">{{ $errors->first() }}</div>
        @endif

        <div class="mb-6 grid gap-4 md:grid-cols-3">
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-pine-200">
                <p class="text-sm text-pine-700">สถานะ</p>
                <p class="mt-2 text-2xl font-semibold {{ $setting->facebook_enabled ? 'text-green-700' : 'text-rose-700' }}">{{ $setting->facebook_enabled ? 'Enabled' : 'Disabled' }}</p>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-pine-200">
                <p class="text-sm text-pine-700">Page Access Token</p>
                <p class="mt-2 break-all text-lg font-semibold text-ink">{{ $setting->masked_page_access_token }}</p>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-pine-200">
                <p class="text-sm text-pine-700">Last Synced</p>
                <p class="mt-2 text-lg font-semibold text-ink">{{ $setting->facebook_last_synced_at?->format('d/m/Y H:i') ?? '-' }}</p>
            </div>
        </div>

        <form method="post" action="{{ route('admin.settings.facebook.update') }}" class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-pine-200">
            @csrf
            <div class="grid gap-5 md:grid-cols-2">
                <label>
                    <span class="text-sm font-semibold text-ink">Page Name</span>
                    <input name="page_name" value="{{ old('page_name', $setting->page_name) }}" class="mt-2 w-full rounded-xl border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200">
                </label>
                <label>
                    <span class="text-sm font-semibold text-ink">Facebook Page ID</span>
                    <input name="facebook_page_id" value="{{ old('facebook_page_id', $setting->facebook_page_id ?: $setting->page_id) }}" class="mt-2 w-full rounded-xl border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200">
                </label>
                <label>
                    <span class="text-sm font-semibold text-ink">Facebook App ID</span>
                    <input name="facebook_app_id" value="{{ old('facebook_app_id', $setting->facebook_app_id ?: $setting->app_id) }}" class="mt-2 w-full rounded-xl border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200">
                </label>
                <label>
                    <span class="text-sm font-semibold text-ink">Facebook App Secret</span>
                    <input name="facebook_app_secret" type="password" autocomplete="new-password" placeholder="ใส่ค่าใหม่เมื่อต้องการเปลี่ยน" class="mt-2 w-full rounded-xl border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200">
                </label>
                <label class="md:col-span-2">
                    <span class="text-sm font-semibold text-ink">Facebook Page Access Token</span>
                    <input name="facebook_page_access_token" type="password" autocomplete="new-password" placeholder="ใส่ค่าใหม่เมื่อต้องการเปลี่ยน Token เท่านั้น" class="mt-2 w-full rounded-xl border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200">
                    <span class="mt-1 block text-xs text-pine-600">ระบบจะแสดง Token แบบ Mask เท่านั้น: {{ $setting->masked_page_access_token }}</span>
                </label>
                <label>
                    <span class="text-sm font-semibold text-ink">Webhook Verify Token</span>
                    <input name="facebook_webhook_verify_token" value="{{ old('facebook_webhook_verify_token', $setting->facebook_webhook_verify_token ?: $setting->webhook_verify_token) }}" class="mt-2 w-full rounded-xl border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200">
                </label>
                <label>
                    <span class="text-sm font-semibold text-ink">Webhook Callback URL</span>
                    <input name="facebook_webhook_callback_url" value="{{ old('facebook_webhook_callback_url', $callbackUrl) }}" class="mt-2 w-full rounded-xl border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200">
                    <span class="mt-1 block break-all text-xs text-pine-600">{{ $callbackUrl }}</span>
                </label>
            </div>

            <div class="mt-6 grid gap-3 sm:grid-cols-2">
                <label class="flex items-center gap-3 rounded-xl bg-pine-50 p-4 ring-1 ring-pine-200">
                    <input type="checkbox" name="facebook_enabled" value="1" @checked(old('facebook_enabled', $setting->facebook_enabled ?: $setting->active)) class="rounded border-pine-300">
                    <span class="text-sm font-semibold text-ink">Enabled / รับ Facebook Lead Ads</span>
                </label>
                <div class="rounded-xl bg-pine-50 p-4 text-sm text-pine-700 ring-1 ring-pine-200">
                    <p class="font-semibold text-ink">Webhook Verify URL</p>
                    <p class="mt-1 break-all">{{ route('webhooks.facebook.verify') }}</p>
                </div>
            </div>

            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                <button name="action" value="save" class="rounded-xl bg-pine-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-pine-500">บันทึกการตั้งค่า</button>
                <button name="action" value="test" class="rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-pine-700 ring-1 ring-pine-200 hover:bg-pine-50">Test Connection</button>
            </div>
        </form>

        <section class="mt-8 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-pine-200">
            <h2 class="text-xl font-semibold text-ink">Webhook Events ล่าสุด</h2>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div class="rounded-xl bg-pine-50 p-4 ring-1 ring-pine-200">
                    <p class="text-sm font-semibold text-pine-700">Raw Event</p>
                    <p class="mt-2 text-sm text-ink">Type: {{ $latestEvent?->event_type ?: '-' }}</p>
                    <p class="mt-1 text-sm text-ink">Leadgen ID: {{ $latestEvent?->leadgen_id ?: '-' }}</p>
                    <p class="mt-1 text-sm text-pine-700">Processed: {{ $latestEvent?->processed_at?->format('d/m/Y H:i') ?? '-' }}</p>
                </div>
                <div class="rounded-xl bg-pine-50 p-4 ring-1 ring-pine-200">
                    <p class="text-sm font-semibold text-pine-700">Legacy Log</p>
                    <p class="mt-2 text-sm text-ink">Type: {{ $latestLog?->event_type ?: '-' }}</p>
                    <p class="mt-1 text-sm text-ink">Status: {{ $latestLog?->status ?: '-' }}</p>
                    @if ($latestLog?->error_message)
                        <p class="mt-1 break-words text-sm text-rose-700">{{ $latestLog->error_message }}</p>
                    @endif
                </div>
            </div>
        </section>
    </div>
</section>
@endsection
