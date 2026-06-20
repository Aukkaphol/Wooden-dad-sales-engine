@extends('layouts.admin', ['title' => 'ตั้งค่า LINE OA | '.company()->display_name])

@section('content')
<section class="bg-pine-50">
    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-semibold text-pine-500">LINE Official Account</p>
                <h1 class="mt-2 text-3xl font-semibold text-ink">ตั้งค่า LINE OA</h1>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-pine-700">แยกการใช้งานระหว่างลิงก์ LINE สำหรับลูกค้าติดต่อ และ LINE Messaging API สำหรับแจ้งเตือนทีมงานภายใน</p>
            </div>
            <a href="{{ route('admin.settings.line.logs') }}" class="w-fit rounded-md bg-white px-4 py-2 text-sm font-semibold text-pine-700 ring-1 ring-pine-200 hover:bg-pine-100">ดูประวัติ Notification</a>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-md bg-green-50 p-4 text-sm font-medium text-green-800 ring-1 ring-green-600/20">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-md bg-rose-50 p-4 text-sm font-medium text-rose-800 ring-1 ring-rose-600/20">{{ $errors->first() }}</div>
        @endif

        <div class="mb-6 grid gap-4 md:grid-cols-3">
            <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-pine-200">
                <p class="text-sm font-semibold text-pine-500">APP_URL</p>
                <p class="mt-2 break-all text-sm font-semibold text-ink">{{ config('app.url') }}</p>
            </div>
            <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-pine-200">
                <p class="text-sm font-semibold text-pine-500">LINE OA URL สำหรับลูกค้า</p>
                <p class="mt-2 break-all text-sm font-semibold text-ink">{{ $company->line_oa_url ?: 'ยังไม่ได้ตั้งค่า' }}</p>
            </div>
            <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-pine-200">
                <p class="text-sm font-semibold text-pine-500">Webhook URL</p>
                <p class="mt-2 break-all text-sm font-semibold text-ink">{{ rtrim(config('app.url'), '/').'/webhooks/line' }}</p>
            </div>
        </div>

        <div class="mb-6 flex flex-col gap-3 sm:flex-row">
            <form method="post" action="{{ route('admin.settings.line.test-notification') }}">
                @csrf
                <button class="w-full rounded-md bg-pine-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-pine-500 sm:w-auto">ทดสอบส่งแจ้งเตือนทีมงาน</button>
            </form>
            @if ($company->line_oa_url)
                <a href="{{ $company->line_oa_url }}" target="_blank" rel="noopener" class="inline-flex justify-center rounded-md bg-white px-5 py-2.5 text-sm font-semibold text-pine-700 ring-1 ring-pine-200 hover:bg-pine-50">ทดสอบลิงก์ LINE สำหรับลูกค้า</a>
            @else
                <span class="inline-flex justify-center rounded-md bg-pine-100 px-5 py-2.5 text-sm font-semibold text-pine-500">ยังไม่มี LINE OA URL สำหรับลูกค้า</span>
            @endif
        </div>

        <form method="post" action="{{ route('admin.settings.line.update') }}" class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-pine-200">
            @csrf
            @method('PATCH')

            <div class="grid gap-6 lg:grid-cols-2">
                <section class="rounded-xl bg-pine-50 p-5 ring-1 ring-pine-200">
                    <h2 class="text-lg font-semibold text-ink">ลิงก์สำหรับลูกค้าติดต่อ</h2>
                    <p class="mt-1 text-sm leading-6 text-pine-700">ใช้กับปุ่ม LINE บนหน้าเว็บไซต์เท่านั้น ไม่เกี่ยวกับการส่ง Notification ภายใน</p>
                    <label class="mt-4 block">
                        <span class="text-sm font-semibold text-ink">LINE OA ID</span>
                        <input name="line_oa_id" value="{{ old('line_oa_id', $company->line_oa_id) }}" placeholder="@woodendad" class="mt-2 w-full rounded-md border-0 bg-white px-3 py-2.5 text-sm ring-1 ring-pine-200">
                    </label>
                    <label class="mt-4 block">
                        <span class="text-sm font-semibold text-ink">LINE OA URL</span>
                        <input name="line_oa_url" value="{{ old('line_oa_url', $company->line_oa_url) }}" placeholder="https://lin.ee/xxxx หรือ https://page.line.me/xxxx" class="mt-2 w-full rounded-md border-0 bg-white px-3 py-2.5 text-sm ring-1 ring-pine-200">
                        <span class="mt-1 block text-xs text-pine-600">รองรับ URL ที่ขึ้นต้นด้วย https:// เช่น lin.ee, line.me หรือ page.line.me</span>
                    </label>
                </section>

                <section class="rounded-xl bg-pine-50 p-5 ring-1 ring-pine-200">
                    <h2 class="text-lg font-semibold text-ink">แจ้งเตือนทีมงานภายใน</h2>
                    <p class="mt-1 text-sm leading-6 text-pine-700">ใช้ LINE Messaging API ส่งเข้าหา staff user หรือ group เท่านั้น ห้ามนำ token ไปแสดงบนหน้า public</p>
                    <label class="mt-4 block">
                        <span class="text-sm font-semibold text-ink">Channel ID</span>
                        <input name="line_channel_id" value="{{ old('line_channel_id', $company->line_channel_id) }}" class="mt-2 w-full rounded-md border-0 bg-white px-3 py-2.5 text-sm ring-1 ring-pine-200">
                    </label>
                    <label class="mt-4 block">
                        <span class="text-sm font-semibold text-ink">Channel Access Token</span>
                        <textarea name="channel_access_token" rows="4" class="mt-2 w-full rounded-md border-0 bg-white px-3 py-2.5 text-sm ring-1 ring-pine-200">{{ old('channel_access_token', $lineSetting->channel_access_token ?: $company->line_channel_access_token) }}</textarea>
                    </label>
                    <label class="mt-4 block">
                        <span class="text-sm font-semibold text-ink">Channel Secret</span>
                        <input name="line_channel_secret" type="password" value="{{ old('line_channel_secret', $company->line_channel_secret) }}" autocomplete="new-password" class="mt-2 w-full rounded-md border-0 bg-white px-3 py-2.5 text-sm ring-1 ring-pine-200">
                    </label>
                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                        <label>
                            <span class="text-sm font-semibold text-ink">Staff User ID</span>
                            <input name="line_staff_notify_user_id" value="{{ old('line_staff_notify_user_id', $company->line_staff_notify_user_id) }}" class="mt-2 w-full rounded-md border-0 bg-white px-3 py-2.5 text-sm ring-1 ring-pine-200">
                        </label>
                        <label>
                            <span class="text-sm font-semibold text-ink">Staff Group ID</span>
                            <input name="line_staff_group_id" value="{{ old('line_staff_group_id', $company->line_staff_group_id) }}" class="mt-2 w-full rounded-md border-0 bg-white px-3 py-2.5 text-sm ring-1 ring-pine-200">
                        </label>
                    </div>
                    <label class="mt-4 block">
                        <span class="text-sm font-semibold text-ink">Admin User ID หรือ Group ID เดิม</span>
                        <input name="admin_recipient_id" value="{{ old('admin_recipient_id', $lineSetting->admin_recipient_id) }}" class="mt-2 w-full rounded-md border-0 bg-white px-3 py-2.5 text-sm ring-1 ring-pine-200">
                    </label>
                </section>
            </div>

            <div class="mt-6 grid gap-4 md:grid-cols-3">
                <label>
                    <span class="text-sm font-semibold text-ink">Production Group ID</span>
                    <input name="production_group_id" value="{{ old('production_group_id', $lineSetting->production_group_id) }}" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200">
                </label>
                <label>
                    <span class="text-sm font-semibold text-ink">Delivery Group ID</span>
                    <input name="delivery_group_id" value="{{ old('delivery_group_id', $lineSetting->delivery_group_id) }}" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200">
                </label>
                <label class="flex items-center gap-3 rounded-md bg-pine-50 px-3 py-3 ring-1 ring-pine-200">
                    <input type="checkbox" name="notifications_enabled" value="1" @checked(old('notifications_enabled', $lineSetting->notifications_enabled)) class="rounded border-pine-300 text-pine-700 focus:ring-pine-500">
                    <span class="text-sm font-semibold text-ink">เปิดใช้งาน Notification</span>
                </label>
            </div>

            <div class="mt-6 rounded-md bg-amber-50 p-4 text-sm leading-6 text-amber-800 ring-1 ring-amber-200">
                ลิงก์สำหรับลูกค้าคือ LINE OA URL ส่วนการแจ้งเตือนภายในใช้ Channel Access Token และ Staff User/Group ID แยกกัน หาก token หรือ recipient ว่าง ระบบจะบันทึก log และ workflow อื่นยังทำงานต่อ
            </div>

            <button class="mt-6 rounded-md bg-pine-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-pine-500">บันทึกการตั้งค่า LINE OA</button>
        </form>

        <section class="mt-8 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-pine-200">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-lg font-semibold text-ink">ประวัติการส่งล่าสุด</h2>
                <a href="{{ route('admin.settings.line.logs') }}" class="text-sm font-semibold text-pine-700 hover:text-ink">ดูทั้งหมด</a>
            </div>
            <div class="mt-5 space-y-3">
                @forelse ($recentLogs as $log)
                    <article class="rounded-md bg-pine-50 p-4">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="font-semibold text-ink">{{ $log->event }} · {{ ucfirst($log->channel) }}</p>
                                <p class="mt-1 text-sm text-pine-700">{{ $log->created_at->format('d/m/Y H:i') }} · {{ $log->recipient_id ?: 'ไม่ระบุผู้รับ' }}</p>
                            </div>
                            <span class="w-fit rounded-full px-3 py-1 text-xs font-semibold {{ $log->status === 'sent' ? 'bg-emerald-50 text-emerald-700' : ($log->status === 'failed' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700') }}">{{ $log->status }}</span>
                        </div>
                    </article>
                @empty
                    <p class="rounded-md border border-dashed border-pine-300 p-6 text-center text-sm text-pine-700">ยังไม่มีประวัติการส่ง Notification</p>
                @endforelse
            </div>
        </section>
    </div>
</section>
@endsection
