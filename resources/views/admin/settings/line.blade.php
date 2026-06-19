@extends('layouts.app', ['title' => 'ตั้งค่า LINE OA | Wooden Dad Design'])

@section('content')
<section class="bg-pine-50">
    <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8">
            <p class="text-sm font-semibold text-pine-500">LINE Official Account</p>
            <h1 class="mt-2 text-3xl font-semibold text-ink">ตั้งค่า LINE OA</h1>
            <p class="mt-2 text-sm text-pine-700">ตั้งค่าการแจ้งเตือนลีดใหม่ ใบเสนอราคาอนุมัติ งานผลิต และนัดส่ง/ติดตั้ง</p>
            <a href="{{ route('admin.settings.line.logs') }}" class="mt-4 inline-flex rounded-md bg-white px-4 py-2 text-sm font-semibold text-pine-700 ring-1 ring-pine-200 hover:bg-pine-100">ดูประวัติการส่ง Notification</a>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-md bg-green-50 p-4 text-sm font-medium text-green-800 ring-1 ring-green-600/20">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-md bg-rose-50 p-4 text-sm font-medium text-rose-800 ring-1 ring-rose-600/20">{{ $errors->first() }}</div>
        @endif

        <form method="post" action="{{ route('admin.settings.line.update') }}" class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
            @csrf
            @method('PATCH')

            <div class="space-y-5">
                <label class="block">
                    <span class="text-sm font-semibold text-ink">Channel Access Token</span>
                    <textarea name="channel_access_token" rows="4" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">{{ old('channel_access_token', $lineSetting->channel_access_token) }}</textarea>
                </label>

                <div class="grid gap-4 md:grid-cols-2">
                    <label>
                        <span class="text-sm font-semibold text-ink">Admin User ID หรือ Group ID</span>
                        <input name="admin_recipient_id" value="{{ old('admin_recipient_id', $lineSetting->admin_recipient_id) }}" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                    </label>
                    <label>
                        <span class="text-sm font-semibold text-ink">Production Group ID</span>
                        <input name="production_group_id" value="{{ old('production_group_id', $lineSetting->production_group_id) }}" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                    </label>
                    <label>
                        <span class="text-sm font-semibold text-ink">Delivery Group ID</span>
                        <input name="delivery_group_id" value="{{ old('delivery_group_id', $lineSetting->delivery_group_id) }}" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                    </label>
                    <label class="flex items-center gap-3 rounded-md bg-pine-50 px-3 py-3">
                        <input type="checkbox" name="notifications_enabled" value="1" @checked(old('notifications_enabled', $lineSetting->notifications_enabled)) class="rounded border-pine-300 text-pine-700 focus:ring-pine-500">
                        <span class="text-sm font-semibold text-ink">เปิดใช้งานการแจ้งเตือน LINE OA</span>
                    </label>
                </div>
            </div>

            <div class="mt-6 rounded-md bg-amber-50 p-4 text-sm text-amber-800 ring-1 ring-amber-200">
                หากไม่ได้ตั้งค่า Token หรือปิดการแจ้งเตือน ระบบ CRM, Lead Form, Quotation และ Production จะยังทำงานตามปกติ และบันทึกสถานะไว้ใน laravel.log
            </div>

            <button class="mt-6 rounded-md bg-pine-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-pine-500">บันทึกการตั้งค่า</button>
        </form>

        <section class="mt-8 rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
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
