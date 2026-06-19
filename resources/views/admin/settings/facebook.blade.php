@extends('layouts.app', ['title' => 'เชื่อมต่อ Facebook | Wooden Dad Design'])

@section('content')
<section class="bg-pine-50">
    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-semibold text-pine-500">การตลาด</p>
                <h1 class="mt-2 text-3xl font-semibold text-ink">เชื่อมต่อ Facebook</h1>
                <p class="mt-2 text-sm leading-6 text-pine-700">ตั้งค่า Facebook Page API เพื่อเตรียมรับ Lead Ads, Messenger และ Webhook สำหรับ Wooden Dad Design CRM</p>
            </div>
            <a href="{{ route('admin.leads.index') }}" class="w-fit rounded-md bg-white px-4 py-2 text-sm font-semibold text-pine-700 ring-1 ring-pine-200 hover:bg-pine-100">ดู CRM Lead</a>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-md bg-green-50 p-4 text-sm font-medium text-green-800 ring-1 ring-green-600/20">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-md bg-rose-50 p-4 text-sm font-medium text-rose-800 ring-1 ring-rose-600/20">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-md bg-rose-50 p-4 text-sm font-medium text-rose-800 ring-1 ring-rose-600/20">{{ $errors->first() }}</div>
        @endif

        <div class="mb-6 grid gap-4 md:grid-cols-3">
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200">
                <p class="text-sm text-pine-700">สถานะการตั้งค่า</p>
                <p class="mt-2 text-2xl font-semibold {{ $setting->active ? 'text-green-700' : 'text-rose-700' }}">{{ $setting->active ? 'เปิดใช้งาน' : 'ปิดใช้งาน' }}</p>
            </div>
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200">
                <p class="text-sm text-pine-700">Page Access Token</p>
                <p class="mt-2 break-all text-lg font-semibold text-ink">{{ $setting->masked_page_access_token }}</p>
            </div>
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200">
                <p class="text-sm text-pine-700">Webhook ล่าสุด</p>
                <p class="mt-2 text-lg font-semibold text-ink">{{ $latestLog?->status ?? 'ยังไม่มีข้อมูล' }}</p>
                @if ($latestLog)
                    <p class="mt-1 text-xs text-pine-600">{{ $latestLog->created_at->format('d/m/Y H:i') }}</p>
                @endif
            </div>
        </div>

        <form method="post" action="{{ route('admin.settings.facebook.update') }}" class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
            @csrf
            <div class="grid gap-5 md:grid-cols-2">
                <label>
                    <span class="text-sm font-semibold text-ink">Page Name</span>
                    <input name="page_name" value="{{ old('page_name', $setting->page_name) }}" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200">
                </label>
                <label>
                    <span class="text-sm font-semibold text-ink">Page ID</span>
                    <input name="page_id" value="{{ old('page_id', $setting->page_id) }}" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200">
                </label>
                <label class="md:col-span-2">
                    <span class="text-sm font-semibold text-ink">Page Access Token</span>
                    <input name="page_access_token" type="password" placeholder="ใส่ค่าใหม่เมื่อต้องการเปลี่ยน Token เท่านั้น" autocomplete="new-password" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200">
                    <span class="mt-1 block text-xs text-pine-600">ระบบจะแสดง Token แบบย่อเท่านั้น: {{ $setting->masked_page_access_token }}</span>
                </label>
                <label>
                    <span class="text-sm font-semibold text-ink">App ID</span>
                    <input name="app_id" value="{{ old('app_id', $setting->app_id) }}" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200">
                </label>
                <label>
                    <span class="text-sm font-semibold text-ink">App Secret</span>
                    <input name="app_secret" type="password" value="{{ old('app_secret', $setting->app_secret) }}" autocomplete="new-password" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200">
                </label>
                <label class="md:col-span-2">
                    <span class="text-sm font-semibold text-ink">Webhook Verify Token</span>
                    <input name="webhook_verify_token" value="{{ old('webhook_verify_token', $setting->webhook_verify_token) }}" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200">
                    <span class="mt-1 block text-xs text-pine-600">ใช้ค่านี้ใน Facebook Developer Webhook Verify URL: {{ route('webhooks.facebook.verify') }}</span>
                </label>
            </div>

            <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <label class="flex items-center gap-3 rounded-md bg-pine-50 p-4 ring-1 ring-pine-200">
                    <input type="checkbox" name="active" value="1" @checked($setting->active) class="rounded border-pine-300">
                    <span class="text-sm font-semibold text-ink">เปิดใช้งาน</span>
                </label>
                <label class="flex items-center gap-3 rounded-md bg-pine-50 p-4 ring-1 ring-pine-200">
                    <input type="checkbox" name="webhook_enabled" value="1" @checked($setting->webhook_enabled) class="rounded border-pine-300">
                    <span class="text-sm font-semibold text-ink">เปิด Webhook</span>
                </label>
                <label class="flex items-center gap-3 rounded-md bg-pine-50 p-4 ring-1 ring-pine-200">
                    <input type="checkbox" name="lead_ads_enabled" value="1" @checked($setting->lead_ads_enabled) class="rounded border-pine-300">
                    <span class="text-sm font-semibold text-ink">เปิด Lead Ads</span>
                </label>
                <label class="flex items-center gap-3 rounded-md bg-pine-50 p-4 ring-1 ring-pine-200">
                    <input type="checkbox" name="messenger_enabled" value="1" @checked($setting->messenger_enabled) class="rounded border-pine-300">
                    <span class="text-sm font-semibold text-ink">เปิด Messenger</span>
                </label>
            </div>

            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                <button name="action" value="save" class="rounded-md bg-pine-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-pine-500">บันทึกการตั้งค่า</button>
                <button name="action" value="test" class="rounded-md bg-white px-5 py-2.5 text-sm font-semibold text-pine-700 ring-1 ring-pine-200 hover:bg-pine-50">ทดสอบการเชื่อมต่อ Facebook</button>
            </div>
        </form>

        <section class="mt-8 rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
            <h2 class="text-xl font-semibold text-ink">สถานะล่าสุดของการเชื่อมต่อ</h2>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div class="rounded-md bg-pine-50 p-4">
                    <p class="text-sm text-pine-700">Lead Ads</p>
                    <p class="mt-1 font-semibold text-ink">{{ $setting->lead_ads_enabled ? 'พร้อมรับ Lead Ads' : 'ยังไม่เปิดใช้งาน' }}</p>
                </div>
                <div class="rounded-md bg-pine-50 p-4">
                    <p class="text-sm text-pine-700">Messenger</p>
                    <p class="mt-1 font-semibold text-ink">{{ $setting->messenger_enabled ? 'พร้อมบันทึกข้อความ Messenger' : 'บันทึก log เท่านั้น' }}</p>
                </div>
            </div>

            @if ($latestLog)
                <div class="mt-5 rounded-md border border-pine-200 p-4">
                    <p class="text-sm text-pine-700">Webhook Log ล่าสุด</p>
                    <p class="mt-2 text-sm text-ink">ประเภท: {{ $latestLog->event_type ?: '-' }}</p>
                    <p class="mt-1 text-sm text-ink">Page ID: {{ $latestLog->page_id ?: '-' }}</p>
                    <p class="mt-1 text-sm text-ink">สถานะ: {{ $latestLog->status }}</p>
                    @if ($latestLog->error_message)
                        <p class="mt-1 break-words text-sm text-rose-700">ข้อผิดพลาด: {{ $latestLog->error_message }}</p>
                    @endif
                </div>
            @endif
        </section>
    </div>
</section>
@endsection
