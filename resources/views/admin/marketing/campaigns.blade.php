@extends('layouts.admin', ['title' => 'Campaigns | '.company()->display_name])

@section('content')
    <section class="bg-pine-50">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div>
                <p class="text-sm font-semibold text-pine-500">Marketing Center</p>
                <h1 class="mt-2 text-3xl font-semibold text-ink">Campaigns</h1>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-pine-700">พื้นที่เตรียมจัดการแคมเปญจาก Website, Facebook, LINE OA, TikTok Lead Form และ Google Analytics ในอนาคต</p>
            </div>

            <div class="mt-8 grid gap-4 md:grid-cols-3">
                @foreach ($sources as $channel => $total)
                    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-pine-200">
                        <p class="text-sm font-medium text-pine-700">{{ $channel }}</p>
                        <p class="mt-2 text-3xl font-semibold text-ink">{{ number_format($total) }}</p>
                        <p class="mt-2 text-sm text-pine-600">ลีดสะสมจากช่องทางนี้</p>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 rounded-2xl border border-dashed border-pine-300 bg-white p-8 text-center text-pine-700">
                ระบบ Campaign จะรองรับ UTM, แคมเปญโฆษณา และ Lead Form เพิ่มเติมโดยใช้โครงสร้างข้อมูลที่เตรียมไว้แล้ว
            </div>
        </div>
    </section>
@endsection
